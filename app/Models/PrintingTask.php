<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintingTask extends Model
{

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
