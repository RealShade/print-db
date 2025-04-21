<?php

namespace App\Http\Requests\Print;

use App\Models\Catalog;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $parent_id
 */
class CatalogRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        return true;
    }

    public function rules() : array
    {
        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:catalogs,id,user_id,' . auth()->id(),
                function($attribute, $value, $fail) {
                    // Если это обновление существующего каталога
                    if ($this->route('catalog')) {
                        $catalogId = $this->route('catalog')->id;

                        // Проверка, что parent_id не равен ID текущего каталога
                        if ($value == $catalogId) {
                            $fail('Каталог не может быть родительским для самого себя.');

                            return;
                        }

                        // Проверка на дочерние каталоги
                        if ($this->isChildCatalog($value, $catalogId)) {
                            $fail('Нельзя выбрать дочерний каталог в качестве родительского.');
                        }
                    }
                },
            ],
        ];

        return $rules;
    }

    /* **************************************** Protected **************************************** */
    /**
     * Проверяет, является ли каталог дочерним для указанного каталога
     *
     * @param int $potentialParentId ID проверяемого родительского каталога
     * @param int $catalogId         ID текущего каталога
     *
     * @return bool
     */
    protected function isChildCatalog(int $potentialParentId, int $catalogId) : bool
    {
        // Получаем все дочерние каталоги текущего каталога
        $childCatalogs = Catalog::where('parent_id', $catalogId)->pluck('id')->toArray();

        if (in_array($potentialParentId, $childCatalogs)) {
            return true;
        }

        // Рекурсивно проверяем дочерние каталоги
        foreach ($childCatalogs as $childId) {
            if ($this->isChildCatalog($potentialParentId, $childId)) {
                return true;
            }
        }

        return false;
    }
}
