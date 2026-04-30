<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AkunGoogle;
use App\Models\Anak;
use App\Models\OrangTua;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login Kader & Bidan — username & password
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('username', $request->username)
            ->whereIn('role', ['Bidan', 'Kader', 'SuperAdmin'])
            ->first();

        if (! $pengguna || ! Hash::check($request->password, $pengguna->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $pengguna->tokens()->delete();
        $token = $pengguna->createToken('sipanda-token')->plainTextToken;

        // Auto-set posyandu aktif jika belum ada
        $this->autoSetPosyanduAktif($pengguna);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'           => $token,
                'role'            => $pengguna->role,
                'id_user'         => $pengguna->id_user,
                'username'        => $pengguna->username,
                'profil'          => $this->getProfil($pengguna),
                'posyandu_aktif'  => $this->getPosyanduAktif($pengguna),
                'posyandu_list'   => $this->getPosyanduList($pengguna),
            ],
        ]);
    }

    /**
     * Login Orang Tua — NIK Balita + Tanggal Lahir
     */
    public function loginOrangTua(Request $request)
    {
        $request->validate([
            'nik_balita' => 'required|string',
            'tgl_lahir'  => 'required|date_format:Y-m-d',
        ]);

        $anak = Anak::where('nik_anak', $request->nik_balita)
            ->whereDate('tgl_lahir', $request->tgl_lahir)
            ->first();

        if (! $anak) {
            throw ValidationException::withMessages([
                'nik_balita' => ['NIK Balita atau Tanggal Lahir tidak ditemukan.'],
            ]);
        }

        $orangTua = OrangTua::where('nik_orang_tua', $anak->nik_orang_tua)->first();

        if (! $orangTua) {
            throw ValidationException::withMessages([
                'nik_balita' => ['Data orang tua tidak ditemukan.'],
            ]);
        }

        $pengguna = Pengguna::where('id_user', $orangTua->id_user)
            ->where('role', 'OrangTua')
            ->first();

        if (! $pengguna) {
            throw ValidationException::withMessages([
                'nik_balita' => ['Akun pengguna tidak ditemukan.'],
            ]);
        }

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
                    'nik'    => $orangTua->nik_orang_tua,
                    'nama'   => $orangTua->nama_ibu,
                    'alamat' => $orangTua->alamat,
                ],
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
     * Login Google OAuth
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

        $this->autoSetPosyanduAktif($pengguna);

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil.',
            'data'    => [
                'token'          => $token,
                'role'           => $pengguna->role,
                'id_user'        => $pengguna->id_user,
                'profil'         => $this->getProfil($pengguna),
                'posyandu_aktif' => $this->getPosyanduAktif($pengguna),
                'posyandu_list'  => $this->getPosyanduList($pengguna),
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
     * Data user login
     */
    public function me(Request $request)
    {
        $pengguna = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id_user'        => $pengguna->id_user,
                'username'       => $pengguna->username,
                'role'           => $pengguna->role,
                'profil'         => $this->getProfil($pengguna),
                'posyandu_aktif' => $this->getPosyanduAktif($pengguna),
                'posyandu_list'  => $this->getPosyanduList($pengguna),
            ],
        ]);
    }

    /**
     * KF-BARU: Ambil daftar posyandu yang bisa diakses user
     * GET /api/v1/auth/posyandu-saya
     */
    public function posyanduSaya(Request $request)
    {
        $pengguna = $request->user();
        $list     = $this->getPosyanduList($pengguna);

        return response()->json([
            'success'        => true,
            'data'           => [
                'posyandu_list'  => $list,
                'posyandu_aktif' => $this->getPosyanduAktif($pengguna),
                'total'          => count($list),
            ],
        ]);
    }

    /**
     * KF-BARU: Set posyandu aktif untuk sesi ini
     * POST /api/v1/auth/set-posyandu
     * Body: { "id_posyandu": 1 }
     */
    public function setPosyandu(Request $request)
    {
        $request->validate([
            'id_posyandu' => 'required|integer|exists:posyandu,id_posyandu',
        ]);

        $pengguna = $request->user();

        // Validasi: user harus punya akses ke posyandu ini
        $bolehAkses = $this->cekAksesPosynadu($pengguna, $request->id_posyandu);

        if (! $bolehAkses) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke posyandu ini.',
            ], 403);
        }

        // Simpan posyandu aktif
        $pengguna->update(['id_posyandu_aktif' => $request->id_posyandu]);

        $posyandu = Posyandu::find($request->id_posyandu);

        return response()->json([
            'success' => true,
            'message' => "Posyandu aktif diubah ke {$posyandu->nama_posyandu}.",
            'data'    => [
                'posyandu_aktif' => [
                    'id_posyandu'   => $posyandu->id_posyandu,
                    'nama_posyandu' => $posyandu->nama_posyandu,
                    'wilayah'       => $posyandu->wilayah,
                    'alamat'        => $posyandu->alamat,
                ],
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

    // ================================================================
    // HELPER METHODS
    // ================================================================

    /**
     * Auto-set posyandu aktif saat pertama login
     * Jika belum ada posyandu_aktif, gunakan posyandu utama
     */
    private function autoSetPosyanduAktif(Pengguna $pengguna): void
    {
        if ($pengguna->id_posyandu_aktif) return; // sudah ada, skip

        $idPosyandu = null;

        // Cek di tabel pivot dulu
        $firstPosyandu = $pengguna->posyanduList()->first();
        if ($firstPosyandu) {
            $idPosyandu = $firstPosyandu->id_posyandu;
        } elseif ($pengguna->id_posyandu) {
            // Fallback ke posyandu utama
            $idPosyandu = $pengguna->id_posyandu;
        }

        if ($idPosyandu) {
            $pengguna->update(['id_posyandu_aktif' => $idPosyandu]);
        }
    }

    /**
     * Cek apakah user boleh akses posyandu tertentu
     */
    private function cekAksesPosynadu(Pengguna $pengguna, int $idPosyandu): bool
    {
        // SuperAdmin bisa akses semua
        if ($pengguna->isSuperAdmin()) return true;

        // Cek di tabel pivot
        $adaDiPivot = $pengguna->posyanduList()
            ->where('posyandu.id_posyandu', $idPosyandu)
            ->exists();

        if ($adaDiPivot) return true;

        // Fallback: cek posyandu utama
        return $pengguna->id_posyandu === $idPosyandu;
    }

    /**
     * Ambil data posyandu aktif
     */
    private function getPosyanduAktif(Pengguna $pengguna): ?array
    {
        $id = $pengguna->getPosyanduAktifId();
        if (! $id) return null;

        $p = Posyandu::find($id);
        if (! $p) return null;

        return [
            'id_posyandu'   => $p->id_posyandu,
            'nama_posyandu' => $p->nama_posyandu,
            'wilayah'       => $p->wilayah,
            'alamat'        => $p->alamat,
        ];
    }

    /**
     * Ambil semua posyandu yang bisa diakses user
     */
    private function getPosyanduList(Pengguna $pengguna): array
    {
        if ($pengguna->isSuperAdmin()) {
            return Posyandu::where('status', 'Aktif')
                ->get(['id_posyandu', 'nama_posyandu', 'wilayah', 'alamat'])
                ->toArray();
        }

        // Ambil dari tabel pivot
        $fromPivot = $pengguna->posyanduList()
            ->where('posyandu.status', 'Aktif')
            ->get(['posyandu.id_posyandu', 'nama_posyandu', 'wilayah', 'alamat'])
            ->toArray();

        if (! empty($fromPivot)) return $fromPivot;

        // Fallback: posyandu utama saja
        if ($pengguna->id_posyandu) {
            $p = Posyandu::find($pengguna->id_posyandu);
            return $p ? [[
                'id_posyandu'   => $p->id_posyandu,
                'nama_posyandu' => $p->nama_posyandu,
                'wilayah'       => $p->wilayah,
                'alamat'        => $p->alamat,
            ]] : [];
        }

        return [];
    }

    /**
     * Get profil berdasarkan role
     */
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