<?php

namespace App\Models\Traits;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\RoleUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasRoles
 *
 * @property Collection $roles
 * @property Collection $permissions
 */
trait HasRoles
{

    /* **************************************** Public **************************************** */
    public function assignRole(UserRole $role) : void
    {
        $this->roles()->create(['role_id' => $role->id()]);
    }

    public function hasPermission(UserPermission $permission) : bool
    {
        return $this->roles->contains(fn($role) => $role->role_id === UserRole::ADMIN->id());
    }

    public function hasRole(UserRole $role) : bool
    {
        return $this->roles->contains('role_id', $role->id());
    }

    public function roles() : HasMany
    {
        return $this->hasMany(RoleUser::class, 'user_id', 'id');
    }

}
