<?php

namespace App\Models;

use App\Enums\PrintJobStatus;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int            $id
 * @property int            $part_id
 * @property int            $task_id
 * @property int            $count_per_set
 * @property int            $count_printed
 * @property Part           $part
 * @property Task           $task
 * @property PrintJob[]     $printJobs
 * @property int            $count_printing
 * @property int            $count_planned
 * @property int            $count_remaining
 */
class PartTask extends Pivot
{
    /* **************************************** Public **************************************** */
    public function part() : BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function printJobs() : BelongsToMany
    {
        return $this->belongsToMany(PrintJob::class, PrintJobPartTask::class, 'part_task_id', 'print_job_id')
            ->withPivot('count_printed');
    }

    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /* **************************************** Getters **************************************** */
    public function getCountPlannedAttribute() : int
    {
        return $this->count_per_set * $this->task->count_set_planned;
    }

    public function getCountPrintingAttribute() : int
    {
        return $this->printJobs()
            ->where('print_jobs.status', '=', PrintJobStatus::PRINTING)
            ->withPivot('count_printed')
            ->sum('print_job_part_task.count_printed');
    }

    public function getCountRemainingAttribute() : int
    {
        return max(0, $this->count_planned - $this->count_printed);
    }

    /* **************************************** Protected **************************************** */
    protected static function booted() : void
    {
        static::updated(function(PartTask $partTask) {
            $task          = $partTask->task;
            $completedSets = $task->count_set_printed;
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
