<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{

    use HasFactory;

    protected $fillable = [
        'token',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /* **************************************** Public **************************************** */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
