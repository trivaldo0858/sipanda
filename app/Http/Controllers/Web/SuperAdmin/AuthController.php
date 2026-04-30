<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login Super Admin
     */
    public function showLogin()
    {
        // Jika sudah login sebagai SuperAdmin, redirect ke dashboard
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.auth.login');
    }

    /**
     * Proses login Super Admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari pengguna dengan role SuperAdmin
        $pengguna = Pengguna::where('username', $request->username)
            ->where('role', 'SuperAdmin')
            ->first();

        // Validasi username
        if (! $pengguna) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username tidak ditemukan atau bukan Super Admin.']);
        }

        // Validasi password
        if (! Hash::check($request->password, $pengguna->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['password' => 'Password salah.']);
        }

        // Login berhasil
        Auth::login($pengguna, $request->boolean('remember'));

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        return redirect()
            ->intended(route('superadmin.dashboard'))
            ->with('success', "Selamat datang, {$pengguna->username}!");
    }

    /**
     * Logout Super Admin
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('superadmin.login')
            ->with('success', 'Anda telah berhasil keluar.');
    }
}