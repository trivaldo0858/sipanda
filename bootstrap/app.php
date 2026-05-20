<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'       => \App\Http\Middleware\RoleMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);

        $middleware->authenticateSessions();

        // Redirect guests — HANYA untuk web, bukan API
        $middleware->redirectGuestsTo(function (Request $request) {
            // Jika request ke API → jangan redirect, return null
            // Laravel akan otomatis return 401 JSON
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            // Hanya redirect untuk web
            return route('superadmin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Pastikan error API selalu return JSON bukan redirect
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Token tidak valid atau sudah expired.',
                ], 401);
            }
        });
    })->create();