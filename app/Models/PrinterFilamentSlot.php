<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * FilamentSlot
 *
 * @property int           $id
 * @property string        $name
 * @property string        $attribute
 * @property string        $description
 * @property int           $filament_spool_id
 * @property int           $printer_id
 * @property FilamentSpool $filamentSpool
 * @property Printer       $printer
 **/
class PrinterFilamentSlot extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'attribute',
        'description',
        'filament_spool_id',
        'printer_id',
    ];

    protected $casts = [
        'filament_spool_id' => 'integer',
        'printer_id'        => 'integer',
    ];

    /* **************************************** Public **************************************** */
    public function filamentSpool() : BelongsTo
    {
        return $this->belongsTo(FilamentSpool::class);
    }

    public function printer() : BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }

}
