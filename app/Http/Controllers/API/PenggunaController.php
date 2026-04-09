<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bidan;
use App\Models\Kader;
use App\Models\OrangTua;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * List semua pengguna (Kader only)
     */
    public function index(Request $request)
    {
        $query = Pengguna::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
        }

        $pengguna = $query->with(['bidan', 'kader', 'orangTua'])->paginate(15);

        return response()->json(['success' => true, 'data' => $pengguna]);
    }

    /**
     * Buat pengguna baru beserta profil sesuai role
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'       => 'required|string|unique:pengguna,username',
            'password'       => 'required|string|min:6',
            'role'           => 'required|in:Bidan,Kader,OrangTua',
            // Bidan
            'nip'            => 'required_if:role,Bidan|string',
            'nama_bidan'     => 'required_if:role,Bidan|string',
            'no_telp'        => 'nullable|string',
            // Kader
            'nama_kader'     => 'required_if:role,Kader|string',
            'wilayah'        => 'nullable|string',
            // OrangTua
            'nik_orang_tua'  => 'required_if:role,OrangTua|string',
            'nama_ibu'       => 'required_if:role,OrangTua|string',
            'alamat'         => 'nullable|string',
        ]);

        $pengguna = DB::transaction(function () use ($request) {
            $user = Pengguna::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            match ($request->role) {
                'Bidan'     => Bidan::create([
                    'nip'        => $request->nip,
                    'id_user'    => $user->id_user,
                    'nama_bidan' => $request->nama_bidan,
                    'no_telp'    => $request->no_telp,
                ]),
                'Kader'     => Kader::create([
                    'id_user'    => $user->id_user,
                    'nama_kader' => $request->nama_kader,
                    'wilayah'    => $request->wilayah,
                ]),
                'OrangTua'  => OrangTua::create([
                    'nik_orang_tua' => $request->nik_orang_tua,
                    'id_user'       => $user->id_user,
                    'nama_ibu'      => $request->nama_ibu,
                    'alamat'        => $request->alamat,
                ]),
            };

            return $user->load(['bidan', 'kader', 'orangTua']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dibuat.',
            'data'    => $pengguna,
        ], 201);
    }

    /**
     * Detail pengguna
     */
    public function show($id)
    {
        $pengguna = Pengguna::with(['bidan', 'kader', 'orangTua', 'akunGoogle'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $pengguna]);
    }

    /**
     * Update pengguna & profil
     */
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'username'   => 'sometimes|string|unique:pengguna,username,' . $id . ',id_user',
            'password'   => 'sometimes|string|min:6',
            'nama_bidan' => 'sometimes|string',
            'no_telp'    => 'nullable|string',
            'nama_kader' => 'sometimes|string',
            'wilayah'    => 'nullable|string',
            'nama_ibu'   => 'sometimes|string',
            'alamat'     => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $pengguna) {
            if ($request->filled('username')) {
                $pengguna->username = $request->username;
            }
            if ($request->filled('password')) {
                $pengguna->password = Hash::make($request->password);
            }
            $pengguna->save();

            // Update profil sesuai role
            if ($pengguna->isBidan() && $pengguna->bidan) {
                $pengguna->bidan->update($request->only(['nama_bidan', 'no_telp']));
            } elseif ($pengguna->isKader() && $pengguna->kader) {
                $pengguna->kader->update($request->only(['nama_kader', 'wilayah']));
            } elseif ($pengguna->isOrangTua() && $pengguna->orangTua) {
                $pengguna->orangTua->update($request->only(['nama_ibu', 'alamat']));
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui.',
            'data'    => $pengguna->fresh(['bidan', 'kader', 'orangTua']),
        ]);
    }

    /**
     * Hapus pengguna
     */
    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
    }
}