<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\OrangTua;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ================================================================
    // LOGIN KADER
    // ================================================================
    public function loginKader(Request $request)
    {
        $request->validate([
            'id_posyandu'    => 'required|integer|exists:posyandu,id_posyandu',
            'password_kader' => 'required|string',
        ]);

        // Ambil password_kader langsung dari DB (bypass hidden)
        $passwordKaderHash = DB::table('posyandu')
            ->where('id_posyandu', $request->id_posyandu)
            ->value('password_kader');

        if (! Hash::check($request->password_kader, $passwordKaderHash)) {
            throw ValidationException::withMessages([
                'password_kader' => ['Password Posyandu salah.'],
            ]);
        }

        $posyandu = Posyandu::findOrFail($request->id_posyandu);

        $kaderUser = Pengguna::firstOrCreate(
            [
                'role'        => 'Kader',
                'id_posyandu' => $posyandu->id_posyandu,
            ],
            [
                'username'          => 'kader_' . $posyandu->id_posyandu,
                'password'          => $passwordKaderHash,
                'id_posyandu_aktif' => $posyandu->id_posyandu,
            ]
        );

        $kaderUser->tokens()->delete();
        $token = $kaderUser->createToken('kader-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'    => $token,
                'role'     => 'Kader',
                'id_user'  => $kaderUser->id_user,
                'posyandu' => [
                    'id_posyandu'    => $posyandu->id_posyandu,
                    'nama_posyandu'  => $posyandu->nama_posyandu,
                    'kecamatan'      => $posyandu->kecamatan,
                    'desa_kelurahan' => $posyandu->desa_kelurahan,
                    'alamat'         => $posyandu->alamat,
                ],
            ],
        ]);
    }

    // ================================================================
    // LOGIN BIDAN
    // ================================================================
    public function loginBidan(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('username', $request->username)
            ->where('role', 'Bidan')
            ->first();

        if (! $pengguna || ! Hash::check($request->password, $pengguna->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $pengguna->tokens()->delete();
        $token = $pengguna->createToken('bidan-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'    => $token,
                'role'     => 'Bidan',
                'id_user'  => $pengguna->id_user,
                'profil'   => $pengguna->bidan ? [
                    'nip'        => $pengguna->bidan->nip,
                    'nama_bidan' => $pengguna->bidan->nama_bidan,
                    'no_telp'    => $pengguna->bidan->no_telp,
                ] : null,
                'posyandu' => $pengguna->posyandu ? [
                    'id_posyandu'   => $pengguna->posyandu->id_posyandu,
                    'nama_posyandu' => $pengguna->posyandu->nama_posyandu,
                ] : null,
            ],
        ]);
    }

    // ================================================================
    // LOGIN ORANG TUA
    // ================================================================
    public function loginOrangTua(Request $request)
    {
        $request->validate([
            'nik_anak'  => 'required|string',
            'tgl_lahir' => 'required|date_format:Y-m-d',
        ]);

        $anak = Anak::where('nik_anak', $request->nik_anak)
            ->whereDate('tgl_lahir', $request->tgl_lahir)
            ->first();

        if (! $anak) {
            throw ValidationException::withMessages([
                'nik_anak' => ['NIK Balita atau Tanggal Lahir tidak sesuai.'],
            ]);
        }

        $orangTua = OrangTua::where('nik_orang_tua', $anak->nik_orang_tua)->first();

        if (! $orangTua || ! $orangTua->id_user) {
            throw ValidationException::withMessages([
                'nik_anak' => ['Akun Orang Tua belum tersedia. Hubungi Kader.'],
            ]);
        }

        $pengguna = Pengguna::where('id_user', $orangTua->id_user)
            ->where('role', 'OrangTua')
            ->first();

        if (! $pengguna) {
            throw ValidationException::withMessages([
                'nik_anak' => ['Akun tidak ditemukan.'],
            ]);
        }

        $pengguna->tokens()->delete();
        $token = $pengguna->createToken('ortu-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'      => $token,
                'role'       => 'OrangTua',
                'id_user'    => $pengguna->id_user,
                'profil'     => [
                    'nik_orang_tua' => $orangTua->nik_orang_tua,
                    'nama_ibu'      => $orangTua->nama_ibu,
                    'alamat'        => $orangTua->alamat,
                ],
                'anak_login' => [
                    'nik_anak'      => $anak->nik_anak,
                    'nama_anak'     => $anak->nama_anak,
                    'tgl_lahir'     => $anak->tgl_lahir->format('Y-m-d'),
                    'jenis_kelamin' => $anak->jenis_kelamin,
                    'umur_bulan'    => $anak->umur_bulan,
                    'umur_format'   => $anak->umur_format,
                ],
            ],
        ]);
    }

    // ================================================================
    // LIST POSYANDU (dropdown login Kader)
    // ================================================================
    public function getPosyanduList()
    {
        $list = Posyandu::select(
            'id_posyandu',
            'nama_posyandu',
            'desa_kelurahan',
            'kecamatan',
            'kabupaten_kota'
        )->orderBy('nama_posyandu')->get();

        return response()->json([
            'success' => true,
            'data'    => $list,
        ]);
    }

    // ================================================================
    // LOGOUT
    // ================================================================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    // ================================================================
    // ME
    // ================================================================
    public function me(Request $request)
    {
        $user = $request->user();
        $data = [
            'id_user' => $user->id_user,
            'role'    => $user->role,
        ];

        if ($user->isBidan() && $user->bidan) {
            $data['profil'] = [
                'nip'        => $user->bidan->nip,
                'nama_bidan' => $user->bidan->nama_bidan,
                'no_telp'    => $user->bidan->no_telp,
            ];
            $data['posyandu'] = $user->posyandu ? [
                'id_posyandu'   => $user->posyandu->id_posyandu,
                'nama_posyandu' => $user->posyandu->nama_posyandu,
            ] : null;
        }

        if ($user->isKader()) {
            $data['posyandu'] = $user->posyandu ? [
                'id_posyandu'    => $user->posyandu->id_posyandu,
                'nama_posyandu'  => $user->posyandu->nama_posyandu,
                'desa_kelurahan' => $user->posyandu->desa_kelurahan,
                'kecamatan'      => $user->posyandu->kecamatan,
            ] : null;
        }

        if ($user->isOrangTua() && $user->orangTua) {
            $data['profil'] = [
                'nik_orang_tua' => $user->orangTua->nik_orang_tua,
                'nama_ibu'      => $user->orangTua->nama_ibu,
            ];
            $data['anak_list'] = $user->orangTua->anak()
                ->select('nik_anak', 'nama_anak', 'tgl_lahir', 'jenis_kelamin')
                ->get()
                ->map(fn ($a) => array_merge($a->toArray(), [
                    'umur_bulan'  => $a->umur_bulan,
                    'umur_format' => $a->umur_format,
                ]));
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ================================================================
    // UBAH PASSWORD (Bidan)
    // ================================================================
    public function ubahPassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    // ================================================================
    // UBAH PASSWORD KADER (password posyandu)
    // Gunakan DB::table langsung untuk bypass $hidden di model Posyandu
    // ================================================================
    public function ubahPasswordKader(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (! $user->id_posyandu) {
            return response()->json([
                'success' => false,
                'message' => 'Posyandu tidak ditemukan.',
            ], 404);
        }

        // Bypass $hidden — ambil langsung dari DB
        $passwordKaderHash = DB::table('posyandu')
            ->where('id_posyandu', $user->id_posyandu)
            ->value('password_kader');

        if (! Hash::check($request->password_lama, $passwordKaderHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $newHash = Hash::make($request->password_baru);

        // Update password_kader di tabel posyandu
        DB::table('posyandu')
            ->where('id_posyandu', $user->id_posyandu)
            ->update(['password_kader' => $newHash, 'updated_at' => now()]);

        // Sync ke semua virtual user Kader di posyandu ini
        Pengguna::where('role', 'Kader')
            ->where('id_posyandu', $user->id_posyandu)
            ->update(['password' => $newHash]);

        return response()->json([
            'success' => true,
            'message' => 'Password Posyandu berhasil diubah.',
        ]);
    }
}