<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            // Jika request meminta JSON (API), berikan response JSON
            if ($request->is('api/*') || $request->expectsJson()) {
                Log::error($e);

                $code = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ? $e->getStatusCode() : 500;

                // Jangan bocorkan detail internal (SQL/path/stack) pada server error saat debug off
                $message = $code >= 500 && ! config('app.debug')
                    ? 'Internal Server Error'
                    : ($e->getMessage() ?: 'Internal Server Error');

                return \App\Helpers\ResponseHelper::error(
                    $message,
                    code: $code
                );
            }

            // Untuk Web, biarkan Laravel menangani AuthenticationException agar bisa redirect ke login
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return null; // Biarkan default handling (redirect ke /login)
            }
        });
    })->create();
