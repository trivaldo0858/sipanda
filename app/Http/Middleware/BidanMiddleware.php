<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BidanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = $request->user();

        // Memastikan pengguna sudah login dan memiliki role Bidan.
        abort_unless(
            $pengguna && $pengguna->isBidan(),
            403,
            'Akses hanya untuk Bidan.'
        );

        // Memastikan akun memiliki profil Bidan.
        $bidan = $pengguna->bidan;

        abort_unless(
            $bidan,
            403,
            'Profil Bidan belum terhubung dengan akun.'
        );

        // Memastikan wilayah tugas Bidan valid.
        abort_unless(
            $bidan->id_posyandu && $bidan->posyandu()->exists(),
            403,
            'Wilayah tugas Bidan belum ditentukan atau tidak valid.'
        );

        return $next($request);
    }
}