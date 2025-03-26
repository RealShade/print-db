<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Auth;

class CheckOwner
{

    /* **************************************** Public **************************************** */
    public function handle(Request $request, Closure $next)
    {
        $user  = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException(__('auth.not_authorized'));
        }

        $route = $request->route();

        // Получаем параметры маршрута
        $parameters = $route->parameters();

        foreach ($parameters as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getAttribute') && $parameter->getAttribute('user_id')) {
                if ($parameter->user_id != $user->id) {
                    throw new AccessDeniedHttpException(__('auth.not_owner'));
                }
            }
        }

        return $next($request);
    }
}
