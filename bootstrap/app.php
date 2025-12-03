<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON responses for requests that expect JSON
        $exceptions->render(function (\Throwable $e, $request) {
            // Check if the request wants JSON by various methods
            $wantsJson = $request->expectsJson() || 
                        $request->wantsJson() || 
                        $request->isJson() || 
                        str_contains($request->header('Accept', ''), 'application/json') ||
                        str_contains($request->header('Content-Type', ''), 'application/json') ||
                        $request->path() === 'checkout' ||
                        str_starts_with($request->path(), 'checkout/');
                        
            if ($wantsJson) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'An error occurred',
                    'detail' => config('app.debug') ? $e->getMessage() : null,
                ], $statusCode ?? 500);
            }
        });
    })->create();
