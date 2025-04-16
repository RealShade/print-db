<?php

namespace App\Models;

use App\Enums\PrinterStatus;
use App\Enums\PrintJobStatus;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int           $id
 * @property string        $name
 * @property PrinterStatus $status
 * @property int           $user_id
 * @property User          $user
 */
class Printer extends Model
{
    use SoftDeletes, HasFactory, HasUser;

    protected $fillable = [
        'name',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => PrinterStatus::class,
    ];

    /* **************************************** Public **************************************** */
    public function filamentSlots() : HasMany
    {
        return $this->hasMany(PrinterFilamentSlot::class);
    }

    public function filamentUsed() : HasMany
    {
        return $this->hasMany(FilamentUsedLog::class)->latest('id');
    }

    public function printJobs() : HasMany
    {
        return $this->hasMany(PrintJob::class);
    }

    public function activeJobs() : HasMany
    {
        return $this->hasMany(PrintJob::class)
            ->where('status', PrintJobStatus::PRINTING);
    }

    public function printingTasks() : HasMany
    {
        return $this->hasMany(PrintingTask::class);
    }

    /* **************************************** Protected **************************************** */
    protected static function booted() : void
    {
        static::deleting(function(Printer $printer) {
            $printer->status = PrinterStatus::DELETED;
            $printer->save();
        });
    }
}
