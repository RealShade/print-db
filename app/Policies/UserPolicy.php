<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
