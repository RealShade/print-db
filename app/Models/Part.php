<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Part extends Model
{
    use HasFactory, HasUser;

    protected $fillable = [
        'name',
        'version',
        'version_date',
        'user_id',
        'catalog_id',
        'stl_filename', // добавлено поле для имени STL-файла
        'stl_original_name', // добавлено поле для оригинального имени STL-файла
    ];

    protected $casts = [
        'version_date' => 'date',
        'catalog_id'   => 'integer',
    ];

    /* **************************************** Public **************************************** */
    public function catalog() : BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function tasks() : BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->using(PartTask::class)
            ->withPivot(['count_per_set', 'count_printed'])
            ->withTimestamps();
    }

    /* **************************************** Getters **************************************** */
    /**
     * Получить полный путь к каталогу детали
     *
     * @param int|null $maxDepth Максимальная глубина (null = без ограничений)
     *
     * @return string
     */
    public function getFullCatalogPath($maxDepth = null)
    {
        if (!$this->catalog) {
            return '';
        }

        $path           = [];
        $currentCatalog = $this->catalog;
        $depth          = 0;

        // Собираем путь снизу вверх
        while ($currentCatalog && ($maxDepth === null || $depth < $maxDepth)) {
            array_unshift($path, $currentCatalog->name);
            $currentCatalog = $currentCatalog->parent;
            $depth++;
        }

        // Если путь был обрезан
        if ($maxDepth !== null && $currentCatalog && $currentCatalog->parent) {
            return '... / ' . implode(' / ', $path);
        }

        return implode(' / ', $path);
    }

}
