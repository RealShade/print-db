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

    public const TASK_PARTS_TABLE = 'task_parts';

    protected $fillable = [
        'external_id',
        'name',
        'sets_count',
        'status',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'status'       => TaskStatus::class,
        'completed_at' => 'datetime',
    ];

    /* **************************************** Public **************************************** */
    public function parts() : BelongsToMany
    {
        return $this->belongsToMany(Part::class, static::TASK_PARTS_TABLE)
            ->withPivot(['quantity_per_set', 'printed_quantity'])
            ->withTimestamps();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* **************************************** Getters **************************************** */
    public function getCompletedSetsCount() : int
    {
        return $this->parts()
            ->get()
            ->map(fn($part) => (int)($part->pivot->printed_quantity / $part->pivot->quantity_per_set))
            ->min() ?? 0;
    }

    /* **************************************** Protected **************************************** */
    protected static function booted() : void
    {
        static::updating(function(Task $task) {
            if ($task->isDirty('status')) {
                if ($task->status === TaskStatus::COMPLETED) {
                    $task->completed_at = now();
                } elseif ($task->getOriginal('status') === TaskStatus::COMPLETED) {
                    $task->completed_at = null;
                }
            }
        });
    }
}
