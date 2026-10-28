<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Menampilkan halaman login bersama.
     */
    public function showLogin()
    {
        // Periksa session Orang Tua terlebih dahulu.
        if (session()->has('orang_tua_nik_anak')) {
            return redirect()->route('orang-tua.dashboard');
        }

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isBidan()) {
                return redirect()->route('bidan.dashboard');
            }

            if ($user->isPosyandu()) {
                return redirect()->route('posyandu.dashboard');
            }

            if ($user->isOrangTua()) {
                return redirect()->route('orang-tua.dashboard');
            }

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }
        }

        $posyandu = \App\Models\Posyandu::orderBy('nama_posyandu')->get();

        return view('auth.login', compact('posyandu'));
    }

    /**
     * Login Bidan menggunakan username dan password.
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

        // Bersihkan session Orang Tua jika ada.
        $request->session()->forget('orang_tua_nik_anak');

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('bidan.dashboard')
            ->with('success', 'Login berhasil.');
    }

    /**
     * Login Orang Tua menggunakan NIK dan tanggal lahir anak.
     */
    public function loginOrangTua(Request $request)
    {
        $request->validate([
            'nik_anak' => 'required|string|digits:16',
            'tgl_lahir' => 'required|date_format:Y-m-d',
        ], [
            'nik_anak.required' => 'NIK anak wajib diisi.',
            'nik_anak.digits' => 'NIK anak harus terdiri dari 16 digit.',
            'tgl_lahir.required' => 'Tanggal lahir anak wajib diisi.',
            'tgl_lahir.date_format' => 'Format tanggal lahir tidak valid.',
        ]);

        $anak = Anak::where('nik_anak', $request->nik_anak)
            ->whereDate('tgl_lahir', $request->tgl_lahir)
            ->first();

        if (!$anak) {
            return back()
                ->withErrors([
                    'nik_anak' => 'NIK atau tanggal lahir anak tidak sesuai.',
                ])
                ->withInput($request->only('nik_anak'));
        }

        // Jangan biarkan autentikasi pengguna sebelumnya tetap aktif.
        Auth::logout();

        // Simpan identitas anak pada session khusus Orang Tua.
        $request->session()->forget('orang_tua_nik_anak');
        $request->session()->regenerate();

        $request->session()->put(
            'orang_tua_nik_anak',
            $anak->nik_anak
        );

        return redirect()->route('orang-tua.dashboard')
            ->with('success', 'Login berhasil.');
    }

    /**
     * Logout Orang Tua.
     */
    public function logoutOrangTua(Request $request)
    {
        $request->session()->forget('orang_tua_nik_anak');
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Kamu berhasil logout.');
    }
}