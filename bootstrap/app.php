<?php

use App\Http\Middleware\AuthenticateApiToken;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\RoleMiddleware;
use App\Providers\FilenamePlaceholderServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function(Middleware $middleware) {
        $middleware->alias([
            'check.user.status' => CheckUserStatus::class,
            'role'              => RoleMiddleware::class,
            'auth.api_token'    => AuthenticateApiToken::class,
        ]);
    })
    ->withProviders([
        FilenamePlaceholderServiceProvider::class,
    ])
    ->withExceptions(function(Exceptions $exceptions) {
        //
    })->create();
