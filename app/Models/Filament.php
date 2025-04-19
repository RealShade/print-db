<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Filament
 *
 * @property int               $id
 * @property string            $name
 * @property int               $filament_vendor_id
 * @property int               $filament_type_id
 * @property array             $colors
 * @property float             $density
 * @property int               $user_id
 * @property FilamentVendor    $vendor
 * @property FilamentType      $type
 * @property FilamentSpool[]   $spools
 */
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
    public static function getForSelect() : array
    {
        $filaments = self::where('user_id', auth()->id())
            ->orderBy('name')
            ->orderBy('filament_type_id')
            ->get();

        $result = [];
        foreach ($filaments as $filament) {
            $vendorName = $filament->vendor->name;

            if (!isset($result[ $vendorName ])) {
                $result[ $vendorName ] = [];
            }

            $result[ $vendorName ][ $filament->id ] = "#{$filament->id} {$filament->name} {$filament->type->name}";
        }

        // Сортировка по имени производителя
        ksort($result);

        return $result;
    }

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
