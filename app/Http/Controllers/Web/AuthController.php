<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Web\AuthController as SharedAuthController;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login bersama.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isBidan()) {
                return redirect('/bidan/dashboard');
            }

            if ($user->isPosyandu()) {
                return redirect('/posyandu/dashboard');
            }

            if ($user->isOrangTua()) {
                return redirect('/orang-tua/dashboard');
            }

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }
        }

        $posyandu = \App\Models\Posyandu::orderBy('nama_posyandu')->get();

        return view('auth.login', compact('posyandu'));
    }

    /**
     * Proses login Bidan.
     */
    public function loginBidan(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('username', $request->username)
            ->where('role', 'Bidan')
            ->first();

        if (
            !$pengguna ||
            !Hash::check($request->password, $pengguna->password)
        ) {
            return back()
                ->withErrors([
                    'username' => 'Username atau kata sandi tidak sesuai.',
                ])
                ->withInput($request->only('username'));
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect('/bidan/dashboard')
            ->with('success', 'Login berhasil.');
    }
}
