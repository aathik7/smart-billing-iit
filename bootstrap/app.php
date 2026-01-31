<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'error' => true,
                'statusCode' => 401,
                'message' => 'Unauthenticated.'
            ], 401);
        });
        $exceptions->render(function (HttpException $e, $request) {
            return response()->json([
                'error' => true,
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        });
        $exceptions->render(function (JWTException $e, $request) {
            return response()->json([
                'message' => 'Token not provided or invalid'
            ], 401);
        });
    })->create();
