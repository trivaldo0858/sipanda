<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'posyandu' => \App\Http\Middleware\PosyanduMiddleware::class,
            'bidan' => \App\Http\Middleware\BidanMiddleware::class,
        ]);

        $middleware->authenticateSessions();

        // Redirect guest sesuai kelompok halaman.
        $middleware->redirectGuestsTo(function (Request $request) {
            // Request API tetap menggunakan respons JSON 401.
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            // Halaman Web Bidan menggunakan login bersama.
            if ($request->is('bidan/*')) {
                return route('login');
            }

            // Halaman Web Posyandu menggunakan login Posyandu.
            if ($request->is('posyandu/*')) {
                return route('posyandu.login');
            }

            // Default halaman Web menggunakan login bersama.
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Pastikan error autentikasi API selalu menghasilkan JSON.
        $exceptions->render(function (
            \Illuminate\Auth\AuthenticationException $e,
            Request $request
        ) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Token tidak valid atau sudah expired.',
                ], 401);
            }
        });
    })
    ->create();