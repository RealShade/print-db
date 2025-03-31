<?php

namespace App\Models;

use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilamentPackaging extends Model
{
    use HasFactory, HasUser;

    protected $fillable = [
        'name',
        'weight',
        'user_id',
    ];

    protected $casts = [
        'weight' => 'integer',
    ];

    /* **************************************** Public **************************************** */
    public function spools() : HasMany
    {
        return $this->hasMany(FilamentSpool::class);
    }
}
