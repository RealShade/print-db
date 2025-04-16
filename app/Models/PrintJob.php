<?php

namespace App\Models;

use App\Enums\PrintJobStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int                   $printer_id    идентификатор принтера (Model\Printer:$id)
 * @property PrintJobStatus        $status        статус печати
 * @property string                $filename     имя файла
 * @property Carbon                $end_time      время окончания печати
 * @property-read  PartTask[]      $partTasks     задачи печати
 * @property-read  Printer         $printer       принтер
 * @property-read  FilamentSpool[] $spools        катушки
 */
class PrintJob extends Model
{

    use HasFactory;

    protected $fillable = [
        'printer_id',
        'status',
        'filename',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'status'     => PrintJobStatus::class,
    ];

    /* **************************************** Public **************************************** */
    public function partTasks() : BelongsToMany
    {
        return $this->belongsToMany(PartTask::class, PrintJobPartTask::class, 'print_job_id', 'part_task_id')
            ->withPivot('count_printed');
    }

    public function printer() : BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }

    public function spools() : BelongsToMany
    {
        return $this->belongsToMany(FilamentSpool::class, PrintJobSpool::class)
            ->withPivot('weight_used');
    }

}
