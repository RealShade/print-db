<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'sets_count',
        'status',
        'completed_at',
        'user_id'
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class, 'task_parts')
            ->withPivot(['quantity_per_set', 'printed_quantity'])
            ->withTimestamps();
    }

    public function getCompletedSetsCount(): int
    {
        return $this->parts()
            ->get()
            ->map(fn($part) => (int)($part->pivot->printed_quantity / $part->pivot->quantity_per_set))
            ->min() ?? 0;
    }
}
