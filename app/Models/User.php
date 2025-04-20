<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Models\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * App\Models\User
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $remember_token
 * @property UserStatus  $status
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable
{

    use Notifiable, SoftDeletes, HasRoles, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* **************************************** Public **************************************** */
    public function apiTokens() : HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    public function catalogs() : HasMany
    {
        return $this->hasMany(Catalog::class);
    }

    public function filamentSpools() : HasMany
    {
        return $this->hasMany(FilamentSpool::class);
    }

    public function parts() : HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function printers() : HasMany
    {
        return $this->hasMany(Printer::class);
    }

    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }

    /* **************************************** Getters **************************************** */
    public function getGravatarURLAttribute() : string
    {
        $hash     = hash('sha256', $this->email);
        $cacheKey = 'gravatar_url_' . $hash;

        return Cache::remember($cacheKey, now()->addHour(), function() use ($hash) {
            $response = Http::get("https://api.gravatar.com/v3/profiles/{$hash}");
            if ($response->successful()) {
                $data = $response->json();

                return $data['avatar_url'] ?? 'https://www.gravatar.com/avatar';
            }

            return 'https://www.gravatar.com/avatar';
        });
    }

    /* **************************************** Protected **************************************** */
    protected static function booted() : void
    {
        static::deleting(function(User $user) {
            $user->status = UserStatus::DELETED;
            $user->save();
        });

        static::restoring(function(User $user) {
            $user->status = UserStatus::NEW;
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts() : array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => UserStatus::class,
            'deleted_at'        => 'datetime',
        ];
    }

}
