<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Facade;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enable API throttle limiter only.
        // Note: statefulApi() is NOT used because this API uses token-based auth,
        // not cookie-based sessions. statefulApi() enables CSRF protection
        // which causes 419 errors for token-based authentication.
        $middleware->throttleApi();
        
        // Trust all proxies for proper HTTPS detection behind nginx
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                                                      \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                                                      \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                                                      \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO);
        
        // Register custom middleware aliases
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
