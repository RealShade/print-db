<?php

namespace App\Http\Requests\Print;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:catalogs,id'],
        ];

        // Проверяем, что родитель не является потомком текущего каталога
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $catalogId = $this->route('catalog')->id;
            $rules['parent_id'][] = "not_in:{$catalogId}";
        }

        return $rules;
    }
}
