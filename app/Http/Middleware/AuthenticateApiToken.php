<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->query('token');

        if ($token) {
            $apiToken = ApiToken::where('token', $token)->first();
            if ($apiToken) {
                if ($apiToken->user->status !== UserStatus::ACTIVE) {
                    return response()->json(['error' => trans('auth.account_inactive')], 403);
                }
                $apiToken->update(['last_used_at' => now()]);
                auth()->login($apiToken->user);
                return $next($request);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
