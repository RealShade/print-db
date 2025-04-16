<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PrintJobSpool extends Pivot
{

    protected $table = 'print_job_spool';

    protected $fillable = [
        'print_job_id',
        'filament_spool_id',
        'weight_used',
    ];

    protected $casts = [
        'weight_used' => 'decimal:4',
    ];

    /* **************************************** Public **************************************** */
    public function filamentSpool() : BelongsTo
    {
        return $this->belongsTo(FilamentSpool::class);
    }

    public function printJob() : BelongsTo
    {
        return $this->belongsTo(PrintJob::class);
    }
}
