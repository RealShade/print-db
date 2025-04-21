<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Catalog extends Model
{
    protected $fillable = ['name', 'parent_id', 'user_id'];

    /* **************************************** Public **************************************** */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function children() : HasMany
    {
        return $this->hasMany(Catalog::class, 'parent_id');
    }

    public function parent() : BelongsTo
    {
        return $this->belongsTo(Catalog::class, 'parent_id');
    }

    public function parts() : HasMany
    {
        return $this->hasMany(Part::class);
    }

    // Рекурсивно получаем дочерние каталоги

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* **************************************** Getters **************************************** */
    public function getFullCatalogPath($maxDepth = null)
    {
        $path           = [];
        $currentCatalog = $this->parent;
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
