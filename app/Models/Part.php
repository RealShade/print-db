<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Part extends Model
{
    protected $fillable = [
        'name',
        'version',
        'version_date',
        'user_id'
    ];

    protected $casts = [
        'version_date' => 'date'
    ];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->using(PartTask::class)
            ->withPivot(['count_per_set', 'count_printed'])
            ->withTimestamps();
    }
}
