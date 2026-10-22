<?php

namespace App\Http\Controllers\Web\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isPosyandu()) {
            return redirect()->route('posyandu.anak.index');
        }

        $posyandu = Posyandu::orderBy('nama_posyandu')->get();

        return view('auth.login', compact('posyandu'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('id_posyandu', $request->id_posyandu)
            ->where('role', 'Posyandu')
            ->first();

        if (
            !$pengguna ||
            !Hash::check($request->password, $pengguna->password)
        ) {
            return back()
                ->withInput($request->only('id_posyandu'))
                ->withErrors([
                    'password' => 'Lokasi Posyandu atau password tidak sesuai.',
                ]);
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('posyandu.anak.index')
            ->with('success', 'Login berhasil.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('posyandu.login')
            ->with('success', 'Kamu berhasil logout.');
    }
}