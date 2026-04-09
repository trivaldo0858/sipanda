<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AkunGoogle;
use App\Models\Bidan;
use App\Models\Kader;
use App\Models\OrangTua;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login — semua role (Bidan, Kader, OrangTua)
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('username', $request->username)->first();

        if (! $pengguna || ! Hash::check($request->password, $pengguna->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Hapus token lama (opsional: single session)
        $pengguna->tokens()->delete();

        $token = $pengguna->createToken('sipanda-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'    => $token,
                'role'     => $pengguna->role,
                'id_user'  => $pengguna->id_user,
                'username' => $pengguna->username,
                'profil'   => $this->getProfil($pengguna),
            ],
        ]);
    }

    /**
     * Login / Register via Google OAuth
     */
    public function loginGoogle(Request $request)
    {
        $request->validate([
            'google_id'    => 'required|string',
            'email_google' => 'required|email',
            'id_user'      => 'required|exists:pengguna,id_user',
        ]);

        $akun = AkunGoogle::updateOrCreate(
            ['google_id' => $request->google_id],
            ['id_user' => $request->id_user, 'email_google' => $request->email_google]
        );

        $pengguna = $akun->pengguna;
        $pengguna->tokens()->delete();
        $token = $pengguna->createToken('sipanda-google-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil.',
            'data'    => [
                'token'   => $token,
                'role'    => $pengguna->role,
                'id_user' => $pengguna->id_user,
                'profil'  => $this->getProfil($pengguna),
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Data user yang sedang login
     */
    public function me(Request $request)
    {
        $pengguna = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id_user'  => $pengguna->id_user,
                'username' => $pengguna->username,
                'role'     => $pengguna->role,
                'profil'   => $this->getProfil($pengguna),
            ],
        ]);
    }

    /**
     * Ubah password
     */
    public function ubahPassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        $pengguna = $request->user();

        if (! Hash::check($request->password_lama, $pengguna->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $pengguna->update(['password' => Hash::make($request->password_baru)]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------
    private function getProfil(Pengguna $pengguna): ?array
    {
        if ($pengguna->isBidan() && $pengguna->bidan) {
            return [
                'nip'        => $pengguna->bidan->nip,
                'nama'       => $pengguna->bidan->nama_bidan,
                'no_telp'    => $pengguna->bidan->no_telp,
            ];
        }

        if ($pengguna->isKader() && $pengguna->kader) {
            return [
                'id_kader'  => $pengguna->kader->id_kader,
                'nama'      => $pengguna->kader->nama_kader,
                'wilayah'   => $pengguna->kader->wilayah,
            ];
        }

        if ($pengguna->isOrangTua() && $pengguna->orangTua) {
            return [
                'nik'     => $pengguna->orangTua->nik_orang_tua,
                'nama'    => $pengguna->orangTua->nama_ibu,
                'alamat'  => $pengguna->orangTua->alamat,
            ];
        }

        return null;
    }
}