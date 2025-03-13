<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $roleEnum = UserRole::from($role);

        if (!$request->user()->hasRole($roleEnum)) {
            abort(403);
        }

        return $next($request);
    }
}
