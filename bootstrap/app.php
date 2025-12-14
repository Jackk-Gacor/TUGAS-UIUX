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

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    | Admin middleware DIHAPUS karena admin tidak memakai login (demo)
    | Tidak ada alias admin.auth di sini
    */

    ->withMiddleware(function (Middleware $middleware): void {
        // Kosong (tidak ada middleware admin)
    })

    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    */

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            $wantsJson =
                $request->expectsJson() ||
                $request->wantsJson() ||
                $request->isJson() ||
                str_contains($request->header('Accept', ''), 'application/json') ||
                str_contains($request->header('Content-Type', ''), 'application/json') ||
                $request->path() === 'checkout' ||
                str_starts_with($request->path(), 'checkout/');

            if ($wantsJson) {
                $statusCode = method_exists($e, 'getStatusCode')
                    ? $e->getStatusCode()
                    : 500;

                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage() ?: 'An error occurred',
                    'detail'  => config('app.debug') ? $e->getMessage() : null,
                ], $statusCode);
            }
        });
    })

    ->create();
