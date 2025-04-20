<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Catalog extends Model
{
    protected $fillable = ['name', 'parent_id', 'user_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Catalog::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Catalog::class, 'parent_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Рекурсивно получаем дочерние каталоги
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
}
