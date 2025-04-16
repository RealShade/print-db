<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Задание на печать
 *
 * @property int         $id
 * @property string      $external_id
 * @property string      $name
 * @property int         $count_set_planned
 * @property TaskStatus  $status
 * @property Carbon|null $completed_at
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 *
 * @property-read Part[] $parts
 * @property-read int    $count_set_printed
 */
class Task extends Model
{
    use SoftDeletes, HasFactory, HasUser;

    protected $fillable = [
        'external_id',
        'name',
        'count_set_planned',
        'status',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'status'       => TaskStatus::class,
        'completed_at' => 'datetime',
    ];

    /* **************************************** Public **************************************** */
    public function partTask() : HasMany
    {
        return $this->hasMany(PartTask::class);
    }

    public function parts() : BelongsToMany
    {
        return $this->belongsToMany(Part::class)
            ->using(PartTask::class)
            ->withPivot(['id', 'count_per_set', 'count_printed'])
            ->withTimestamps();
    }

    /* **************************************** Getters **************************************** */
    public function getCountSetPrintedAttribute() : int
    {
        return $this->parts()
            ->get()
            ->map(fn($part) => (int)($part->pivot->count_printed / $part->pivot->count_per_set))
            ->min() ?? 0;
    }
    public function getCountSetPrintingAttribute()
    {
        return $this->parts()
            ->get()
            ->map(fn($part) => (int)($part->pivot->count_printing / $part->pivot->count_per_set))
            ->min() ?? 0;
    }

    public function isPrinting() : bool
    {
        return $this->parts()->get()->map(fn($part) => $part->pivot->count_printing)->sum() > 0;
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
