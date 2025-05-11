<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\PassportServiceProvider;
use Illuminate\Auth\AuthenticationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ .'/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $headers = getallheaders();
        $body = file_get_contents('php://input');
        file_put_contents(
            __DIR__ . '/../storage/logs/bootstrap_app.log',
            '[' . date('Y-m-d H:i:s') . "] Headers: " . json_encode($headers) . PHP_EOL .
            "Body: $body" . PHP_EOL . str_repeat('-', 80) . PHP_EOL,
            FILE_APPEND
        );
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth:api' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
            'cors' => \Illuminate\Http\Middleware\HandleCors::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'token.from.cookie' => \App\Http\Middleware\TokenFromCookie::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'Token tidak valid atau tidak diberikan.',
            ], 401);
        });
    })
    ->withProviders([
        App\Providers\AuthServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        PassportServiceProvider::class
    ])
    ->create();
