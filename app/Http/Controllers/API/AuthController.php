<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AkunGoogle;
use App\Models\Anak;
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
     * Login Kader & Bidan — menggunakan username & password
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('username', $request->username)
            ->whereIn('role', ['Bidan', 'Kader'])
            ->first();

        if (! $pengguna || ! Hash::check($request->password, $pengguna->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

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
     * Login Orang Tua — menggunakan NIK Balita + Tanggal Lahir Balita
     *
     * Alur:
     * 1. Cari anak berdasarkan nik_anak + tgl_lahir
     * 2. Ambil orang tua dari anak tersebut
     * 3. Ambil pengguna dari orang tua
     * 4. Generate token dan kembalikan response
     */
    public function loginOrangTua(Request $request)
    {
        $request->validate([
            'nik_balita' => 'required|string',
            'tgl_lahir'  => 'required|date_format:Y-m-d',
        ]);

        // Cari anak berdasarkan NIK + tanggal lahir
        $anak = Anak::where('nik_anak', $request->nik_balita)
            ->whereDate('tgl_lahir', $request->tgl_lahir)
            ->first();

        if (! $anak) {
            throw ValidationException::withMessages([
                'nik_balita' => ['NIK Balita atau Tanggal Lahir tidak ditemukan.'],
            ]);
        }

        // Ambil data orang tua
        $orangTua = OrangTua::where('nik_orang_tua', $anak->nik_orang_tua)->first();

        if (! $orangTua) {
            throw ValidationException::withMessages([
                'nik_balita' => ['Data orang tua tidak ditemukan.'],
            ]);
        }

        // Ambil akun pengguna orang tua
        $pengguna = Pengguna::where('id_user', $orangTua->id_user)
            ->where('role', 'OrangTua')
            ->first();

        if (! $pengguna) {
            throw ValidationException::withMessages([
                'nik_balita' => ['Akun pengguna tidak ditemukan.'],
            ]);
        }

        // Hapus token lama & buat token baru
        $pengguna->tokens()->delete();
        $token = $pengguna->createToken('sipanda-ortu-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'    => $token,
                'role'     => $pengguna->role,
                'id_user'  => $pengguna->id_user,
                'username' => $pengguna->username,
                'profil'   => [
                    'nik'      => $orangTua->nik_orang_tua,
                    'nama'     => $orangTua->nama_ibu,
                    'alamat'   => $orangTua->alamat,
                ],
                // Langsung kirim data anak yang digunakan untuk login
                'anak_login' => [
                    'nik_anak'      => $anak->nik_anak,
                    'nama_anak'     => $anak->nama_anak,
                    'tgl_lahir'     => $anak->tgl_lahir,
                    'jenis_kelamin' => $anak->jenis_kelamin,
                    'umur_bulan'    => $anak->umur_bulan,
                ],
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
     * Ubah password (Kader & Bidan)
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
                'nip'     => $pengguna->bidan->nip,
                'nama'    => $pengguna->bidan->nama_bidan,
                'no_telp' => $pengguna->bidan->no_telp,
            ];
        }

        if ($pengguna->isKader() && $pengguna->kader) {
            return [
                'id_kader' => $pengguna->kader->id_kader,
                'nama'     => $pengguna->kader->nama_kader,
                'wilayah'  => $pengguna->kader->wilayah,
            ];
        }

        if ($pengguna->isOrangTua() && $pengguna->orangTua) {
            return [
                'nik'    => $pengguna->orangTua->nik_orang_tua,
                'nama'   => $pengguna->orangTua->nama_ibu,
                'alamat' => $pengguna->orangTua->alamat,
            ];
        }

        return null;
    }
}