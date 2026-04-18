<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Penggunaan: route->middleware('role:Bidan,Kader')
     * SuperAdmin otomatis bisa akses semua route yang protected
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // SuperAdmin bisa akses semua endpoint
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Role tidak diizinkan.',
            ], 403);
        }

        return $next($request);
    }
}