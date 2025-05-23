<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilamentVendor extends Model
{
    use HasFactory, HasUser;

    protected $fillable = [
        'name',
        'rate',
        'comment',
        'user_id',
    ];

    /* **************************************** Public **************************************** */
    public function filaments() : HasMany
    {
        return $this->hasMany(Filament::class);
    }
}
