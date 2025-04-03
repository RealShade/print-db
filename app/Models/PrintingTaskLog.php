<?php

namespace App\Models;

use App\Enums\PrintTaskEventSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintingTaskLog extends Model
{

    protected $fillable = [
        'part_task_id',
        'printer_id',
        'count',
        'event_source',
    ];

    protected $casts = [
        'event_source' => PrintTaskEventSource::class,
    ];

    /* **************************************** Public **************************************** */
    public function partTask() : BelongsTo
    {
        return $this->belongsTo(PartTask::class, 'part_task_id');
    }

    public function printer() : BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }
}
