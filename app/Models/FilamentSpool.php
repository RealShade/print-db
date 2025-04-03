<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function filamentUsed() : HasMany
    {
        return $this->hasMany(FilamentUsedLog::class)->latest('id');
    }

    public function packaging() : BelongsTo
    {
        return $this->belongsTo(FilamentPackaging::class, 'filament_packaging_id');
    }

    /* **************************************** Getters **************************************** */
    public function getRemainingWeightAttribute() : float
    {
        return round($this->weight_initial - ($this->weight_used ?? 0), 4);
    }

    public function getUsedPercentageAttribute() : float
    {
        if (!$this->weight_initial) {
            return 0;
        }

        return ($this->weight_used ?? 0) / $this->weight_initial * 100;
    }
}
