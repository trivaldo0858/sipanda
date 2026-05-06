<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Bidan;
use App\Models\Kader;
use App\Models\OrangTua;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * List semua pengguna dengan fitur pencarian nama lengkap
     */
    public function index(Request $request)
    {
        $pengguna = Pengguna::with(['bidan', 'kader', 'orangTua', 'posyandu', 'posyanduList'])
            ->whereIn('role', ['Bidan', 'Kader', 'OrangTua'])
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when(
                $request->id_posyandu,
                fn($q) =>
                $q->where(function ($q2) use ($request) {
                    $q2->where('id_posyandu', $request->id_posyandu)
                        ->orWhereHas(
                            'posyanduList',
                            fn($q3) =>
                            $q3->where('posyandu.id_posyandu', $request->id_posyandu)
                        );
                })
            )
            // PERBAIKAN: Pencarian sekarang mencakup Nama Lengkap di tabel relasi
            ->when($request->search, function ($q) use ($request) {
                $searchTerm = '%' . $request->search . '%';
                $q->where(function ($query) use ($searchTerm) {
                    $query->where('username', 'like', $searchTerm)
                        ->orWhereHas('bidan', fn($q2) => $q2->where('nama_bidan', 'like', $searchTerm))
                        ->orWhereHas('kader', fn($q2) => $q2->where('nama_kader', 'like', $searchTerm))
                        ->orWhereHas('orangTua', fn($q2) => $q2->where('nama_ibu', 'like', $searchTerm));
                });
            })
            ->paginate(15)
            ->withQueryString();

        $posyanduList = Posyandu::where('status', 'Aktif')->get();

        return view('superadmin.pengguna.index', compact('pengguna', 'posyanduList'));
    }

    /**
     * Form tambah pengguna
     */
    public function create()
    {
        $posyanduList = Posyandu::where('status', 'Aktif')->get();
        return view('superadmin.pengguna.form', [
            'posyanduList' => $posyanduList,
            'pengguna_single' => null
        ]);
    }

    /**
     * Simpan pengguna baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:pengguna,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Bidan,Kader,OrangTua',
            'nama' => 'required|string',
            'id_posyandu' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $user = Pengguna::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'id_posyandu' => $request->id_posyandu[0], 
                'id_posyandu_aktif' => $request->id_posyandu[0],
            ]);

            match ($request->role) {
                'Bidan' => Bidan::create([
                    'id_user' => $user->id_user,
                    'nama_bidan' => $request->nama,
                    'id_posyandu' => $request->id_posyandu[0],
                    'nip' => '-', 
                ]),
                'Kader' => Kader::create([
                    'id_user' => $user->id_user,
                    'nama_kader' => $request->nama,
                    'id_posyandu' => $request->id_posyandu[0],
                ]),
                'OrangTua' => OrangTua::create([
                    'id_user' => $user->id_user,
                    'nama_ibu' => $request->nama,
                ]),
            };

            $user->posyanduList()->attach($request->id_posyandu);
        });

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Akun Berhasil Dibuat');
    }

    /**
     * Form edit pengguna
     */
    public function edit($id)
    {
        $pengguna_single = Pengguna::with(['bidan', 'kader', 'orangTua', 'posyanduList'])
            ->findOrFail($id);
        $posyanduList = Posyandu::where('status', 'Aktif')->get();

        return view('superadmin.pengguna.form', compact('pengguna_single', 'posyanduList'));
    }

    /**
     * Update pengguna
     */
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'username' => 'required|string|unique:pengguna,username,' . $id . ',id_user',
            'password' => 'nullable|string|min:6',
            'nama' => 'required|string',
            'id_posyandu' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $pengguna) {
            $userData = ['username' => $request->username];
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $userData['id_posyandu'] = $request->id_posyandu[0];
            $pengguna->update($userData);

            if ($pengguna->isBidan() && $pengguna->bidan) {
                $pengguna->bidan->update([
                    'nama_bidan' => $request->nama, 
                    'id_posyandu' => $request->id_posyandu[0]
                ]);
            } elseif ($pengguna->isKader() && $pengguna->kader) {
                $pengguna->kader->update([
                    'nama_kader' => $request->nama, 
                    'id_posyandu' => $request->id_posyandu[0]
                ]);
            } elseif ($pengguna->isOrangTua() && $pengguna->orangTua) {
                $pengguna->orangTua->update([
                    'nama_ibu' => $request->nama
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
            return back()->with('error', "{$pengguna->username} sudah memiliki akses ke {$posyandu->nama_posyandu}.");
        }

        $pengguna->posyanduList()->attach($request->id_posyandu);

        return back()->with('success', "Akses {$posyandu->nama_posyandu} berhasil ditambahkan.");
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
            return back()->with('error', 'Tidak dapat menghapus posyandu satu-satunya milik pengguna ini.');
        }

        $pengguna->posyanduList()->detach($request->id_posyandu);

        if ($pengguna->id_posyandu_aktif == $request->id_posyandu) {
            $pengguna->update(['id_posyandu_aktif' => $pengguna->id_posyandu]);
        }

        return back()->with('success', "Akses {$posyandu->nama_posyandu} berhasil dicabut.");
    }
}