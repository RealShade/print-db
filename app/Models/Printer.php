<?php

namespace App\Models;

use App\Enums\PrinterStatus;
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
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => PrinterStatus::class,
    ];

    /* **************************************** Public **************************************** */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function printingTasks() : HasMany|Printer
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
