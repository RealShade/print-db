<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filament extends Model
{
    use HasFactory, HasUser;

    protected $fillable = [
        'name',
        'filament_vendor_id',
        'filament_type_id',
        'colors',
        'density',
        'user_id',
    ];

    protected $casts = [
        'colors'  => 'array',
        'density' => 'decimal:2',
    ];

    /* **************************************** Public **************************************** */
    public function spools() : HasMany
    {
        return $this->hasMany(FilamentSpool::class);
    }

    public function type() : BelongsTo
    {
        return $this->belongsTo(FilamentType::class, 'filament_type_id');
    }

    public function vendor() : BelongsTo
    {
        return $this->belongsTo(FilamentVendor::class, 'filament_vendor_id');
    }
}
