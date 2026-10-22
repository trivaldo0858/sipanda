<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PosyanduMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('posyandu.login');
        }

        if (!Auth::user()->isPosyandu()) {
            abort(403, 'Akses hanya untuk pengguna Posyandu.');
        }

        return $next($request);
    }
}
