<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Penggunaan di route: middleware('role:Kader,Bidan')
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

        // Cocokkan role user dengan yang diizinkan
        if (! in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Role tidak diizinkan.',
            ], 403);
        }

        return $next($request);
    }
}