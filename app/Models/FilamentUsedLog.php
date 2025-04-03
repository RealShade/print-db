<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilamentUsedLog extends Model
{
    protected $fillable = [
        'filament_spool_id',
        'printer_id',
        'part_task_id',
        'weight_used',
    ];

    protected $casts = [
        'weight_used' => 'decimal:4',
    ];

    /* **************************************** Public **************************************** */
    public function spool() : BelongsTo
    {
        return $this->belongsTo(FilamentSpool::class, 'filament_spool_id');
    }

    public function printer() : BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }

    public function partTask() : BelongsTo
    {
        return $this->belongsTo(PartTask::class);
    }

}
