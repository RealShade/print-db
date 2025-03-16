<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PartTask extends Pivot
{
    /* **************************************** Public **************************************** */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /* **************************************** Protected **************************************** */
    protected static function booted() : void
    {
        static::updated(function(PartTask $partTask) {
            $task          = $partTask->task;
            $completedSets = $task->getCompletedSetsCount();
            $plannedSets   = $task->count_set_planned;

            if ($completedSets >= $plannedSets) {
                if (in_array($task->status, [TaskStatus::NEW, TaskStatus::IN_PROGRESS])) {
                    $task->update(['status' => TaskStatus::PRINTED]);
                }
            } elseif ($task->status === TaskStatus::PRINTED) {
                $task->update(['status' => TaskStatus::IN_PROGRESS]);
            }
        });
    }
}
