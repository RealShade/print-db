<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->status !== UserStatus::ACTIVE) {
            auth()->logout();
            return redirect()
                ->route('login')
                ->withErrors(['email' => trans('auth.account_inactive')]);
        }

        return $next($request);
    }
}
