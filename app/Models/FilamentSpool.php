<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * FilamentSpool
 *
 * @property int                    $id
 * @property int                    $filament_id
 * @property int                    $filament_packaging_id
 * @property float                  $weight_initial
 * @property float                  $weight_used
 * @property Carbon|null            $date_first_used
 * @property Carbon|null            $date_last_used
 * @property float                  $cost
 * @property int                    $user_id
 * @property Filament               $filament
 * @property FilamentPackaging      $packaging
 * @property-read float             $weight_remaining
 */
class FilamentSpool extends Model
{
    use HasFactory, HasUser;

    protected $fillable = [
        'filament_id',
        'filament_packaging_id',
        'weight_initial',
        'weight_used',
        'date_first_used',
        'date_last_used',
        'cost',
        'user_id',
    ];

    protected $casts = [
        'weight_initial'  => 'decimal:4',
        'weight_used'     => 'decimal:4',
        'date_first_used' => 'datetime',
        'date_last_used'  => 'datetime',
        'cost'            => 'decimal:2',
    ];

    /* **************************************** Public **************************************** */
    public function filament() : BelongsTo
    {
        return $this->belongsTo(Filament::class);
    }

    public function packaging() : BelongsTo
    {
        return $this->belongsTo(FilamentPackaging::class, 'filament_packaging_id');
    }

    /* **************************************** Getters **************************************** */
    public function getUsedPercentageAttribute() : float
    {
        if (!$this->weight_initial) {
            return 0;
        }

        return ($this->weight_used ?? 0) / $this->weight_initial * 100;
    }

    public function getWeightRemainingAttribute() : float
    {
        return round(max(0, $this->weight_initial - $this->weight_used), 4);
    }

    /* **************************************** Protected **************************************** */
    protected static function boot() : void
    {
        parent::boot();

        static::creating(function($model) {
            if ($model->filament_packaging_id) {
                $packaging = FilamentPackaging::find($model->filament_packaging_id);
                if ($packaging) {
                    $model->weight_initial = $packaging->weight;
                }
            }
        });
    }

}
