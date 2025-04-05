<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * FilamentLoaded
 * @property int         $id
 * @property string      $name
 * @property string      $attribute
 * @property string      $description
 * @property int         $filament_spool_id
 * @property int         $printer_id
 **/
class FilamentLoaded extends Model
{

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
