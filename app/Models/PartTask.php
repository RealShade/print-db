<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PartTask extends Pivot
{
    /* **************************************** Public **************************************** */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function printingTasks()
    {
        return $this->hasMany(PrintingTask::class, 'part_task_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /* **************************************** Getters **************************************** */
    public function getPrintingCountAttribute() : int
    {
        return $this->printingTasks->sum('count');
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
