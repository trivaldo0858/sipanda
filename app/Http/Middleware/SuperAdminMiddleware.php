<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya Super Admin.',
                ], 403);
            }
            return redirect()->route('superadmin.login')
                ->with('error', 'Silakan login sebagai Super Admin.');
        }

        return $next($request);
    }
}