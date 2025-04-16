<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Привязка задания к печати
 *
 * @property int $print_job_id идентификатор задания на печать (Model\PrintJob:$id)
 * @property int $part_task_id  идентификатор задания (Model\PartTask:$id)
 * @property int $count_printed количество напечатанных экземпляров
 *
 * @property-read PartTask $partTask
 * @property-read PrintJob $printJob
 */
class PrintJobPartTask extends Pivot
{

    protected $fillable = [
        'print_job_id',
        'part_task_id',
        'count_printed',
    ];

    protected $casts = [
        'count_printed' => 'integer',
    ];

    /* **************************************** Public **************************************** */
    public function partTask() : BelongsTo
    {
        return $this->belongsTo(PartTask::class);
    }

    public function printJob() : BelongsTo
    {
        return $this->belongsTo(PrintJob::class);
    }

}
