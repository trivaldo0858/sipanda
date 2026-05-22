<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Bidan;
use App\Models\OrangTua;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * List semua pengguna
     * Kader tidak punya akun personal — hanya tampilkan Bidan & OrangTua
     */
    public function index(Request $request)
    {
        $pengguna = Pengguna::with(['bidan', 'orangTua', 'posyandu', 'posyanduList'])
            ->whereIn('role', ['Bidan', 'Kader', 'OrangTua'])
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->id_posyandu, fn($q) =>
                $q->where(function ($q2) use ($request) {
                    $q2->where('id_posyandu', $request->id_posyandu)
                       ->orWhereHas('posyanduList', fn($q3) =>
                           $q3->where('posyandu.id_posyandu', $request->id_posyandu)
                       );
                })
            )
            ->when($request->search, function ($q) use ($request) {
                $searchTerm = '%' . $request->search . '%';
                $q->where(function ($query) use ($searchTerm) {
                    $query->where('username', 'like', $searchTerm)
                        // Hanya Bidan & OrangTua yang punya relasi profil
                        ->orWhereHas('bidan', fn($q2) =>
                            $q2->where('nama_bidan', 'like', $searchTerm)
                        )
                        ->orWhereHas('orangTua', fn($q2) =>
                            $q2->where('nama_ibu', 'like', $searchTerm)
                        );
                });
            })
            ->paginate(15)
            ->withQueryString();

        $posyanduList = Posyandu::all();

        return view('superadmin.pengguna.index', compact('pengguna', 'posyanduList'));
    }

    /**
     * Form tambah pengguna
     */
    public function create()
    {
        $posyanduList = Posyandu::all();
        return view('superadmin.pengguna.form', [
            'posyanduList'    => $posyanduList,
            'pengguna_single' => null,
        ]);
    }

    /**
     * Simpan pengguna baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'    => 'required|string|unique:pengguna,username',
            'password'    => 'required|string|min:6',
            'role'        => 'required|in:Bidan,OrangTua',
            'nama'        => 'required|string',
            'id_posyandu' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $user = Pengguna::create([
                'username'          => $request->username,
                'password'          => Hash::make($request->password),
                'role'              => $request->role,
                'id_posyandu'       => $request->id_posyandu[0],
                'id_posyandu_aktif' => $request->id_posyandu[0],
            ]);

            match ($request->role) {
                'Bidan' => Bidan::create([
                    'nip'        => $request->nip ?? ('NIP-' . time()),
                    'id_user'    => $user->id_user,
                    'nama_bidan' => $request->nama,
                    'no_telp'    => $request->no_telp,
                ]),
                'OrangTua' => OrangTua::create([
                    'nik_orang_tua' => $request->nik_orang_tua ?? ('NIK-' . time()),
                    'id_user'       => $user->id_user,
                    'nama_ibu'      => $request->nama,
                    'alamat'        => $request->alamat,
                ]),
            };

            $user->posyanduList()->attach($request->id_posyandu);
        });

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Form edit pengguna
     */
    public function edit($id)
    {
        $pengguna_single = Pengguna::with(['bidan', 'orangTua', 'posyanduList'])
            ->findOrFail($id);

        $posyanduList = Posyandu::all();

        return view('superadmin.pengguna.form', compact('pengguna_single', 'posyanduList'));
    }

    /**
     * Update pengguna
     */
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'username'    => 'required|string|unique:pengguna,username,' . $id . ',id_user',
            'password'    => 'nullable|string|min:6',
            'nama'        => 'required|string',
            'id_posyandu' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $pengguna) {
            $userData = [
                'username'    => $request->username,
                'id_posyandu' => $request->id_posyandu[0],
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $pengguna->update($userData);

            if ($pengguna->isBidan() && $pengguna->bidan) {
                $pengguna->bidan->update([
                    'nama_bidan' => $request->nama,
                    'no_telp'    => $request->no_telp,
                ]);
            } elseif ($pengguna->isOrangTua() && $pengguna->orangTua) {
                $pengguna->orangTua->update([
                    'nama_ibu' => $request->nama,
                    'alamat'   => $request->alamat,
                ]);
            }

            $pengguna->posyanduList()->sync($request->id_posyandu);
        });

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna
     */
    public function destroy($id)
    {
        Pengguna::findOrFail($id)->delete();

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Assign posyandu tambahan ke pengguna
     */
    public function assignPosyandu(Request $request, $id)
    {
        $request->validate([
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
        ]);

        $pengguna = Pengguna::with('posyanduList')->findOrFail($id);
        $posyandu = Posyandu::findOrFail($request->id_posyandu);

        $sudahAda = $pengguna->posyanduList()
            ->where('posyandu.id_posyandu', $request->id_posyandu)
            ->exists();

        if ($sudahAda) {
            return back()->with('error',
                "{$pengguna->username} sudah memiliki akses ke {$posyandu->nama_posyandu}."
            );
        }

        $pengguna->posyanduList()->attach($request->id_posyandu);

        return back()->with('success',
            "Akses {$posyandu->nama_posyandu} berhasil ditambahkan."
        );
    }

    /**
     * Cabut akses posyandu dari pengguna
     */
    public function removePosyandu(Request $request, $id)
    {
        $request->validate([
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
        ]);

        $pengguna = Pengguna::findOrFail($id);
        $posyandu = Posyandu::findOrFail($request->id_posyandu);

        $total = $pengguna->posyanduList()->count();
        if ($total <= 1) {
            return back()->with('error',
                'Tidak dapat menghapus posyandu satu-satunya milik pengguna ini.'
            );
        }

        $pengguna->posyanduList()->detach($request->id_posyandu);

        if ($pengguna->id_posyandu_aktif == $request->id_posyandu) {
            $pengguna->update(['id_posyandu_aktif' => $pengguna->id_posyandu]);
        }

        return back()->with('success',
            "Akses {$posyandu->nama_posyandu} berhasil dicabut."
        );
    }
}