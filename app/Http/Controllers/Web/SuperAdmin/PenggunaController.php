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
     * List semua pengguna
     */
    public function index(Request $request)
    {
        $pengguna = Pengguna::with(['bidan', 'kader', 'orangTua', 'posyandu', 'posyanduList'])
            ->whereIn('role', ['Bidan', 'Kader', 'OrangTua'])
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->id_posyandu, fn ($q) =>
                $q->where(function ($q2) use ($request) {
                    $q2->where('id_posyandu', $request->id_posyandu)
                       ->orWhereHas('posyanduList', fn ($q3) =>
                           $q3->where('posyandu.id_posyandu', $request->id_posyandu)
                       );
                })
            )
            ->when($request->search, fn ($q) =>
                $q->where('username', 'like', '%' . $request->search . '%')
            )
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
        return view('superadmin.pengguna.form', compact('posyanduList'));
    }

    /**
     * Simpan pengguna baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'      => 'required|string|unique:pengguna,username',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:Bidan,Kader,OrangTua',
            'id_posyandu'   => 'nullable|exists:posyandu,id_posyandu',
            'nip'           => 'required_if:role,Bidan|string',
            'nama_bidan'    => 'required_if:role,Bidan|string',
            'no_telp'       => 'nullable|string',
            'nama_kader'    => 'required_if:role,Kader|string',
            'wilayah'       => 'nullable|string',
            'nik_orang_tua' => 'required_if:role,OrangTua|string',
            'nama_ibu'      => 'required_if:role,OrangTua|string',
            'alamat'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = Pengguna::create([
                'username'          => $request->username,
                'password'          => Hash::make($request->password),
                'role'              => $request->role,
                'id_posyandu'       => $request->id_posyandu,
                'id_posyandu_aktif' => $request->id_posyandu,
            ]);

            match ($request->role) {
                'Bidan'    => Bidan::create([
                    'nip'         => $request->nip,
                    'id_user'     => $user->id_user,
                    'nama_bidan'  => $request->nama_bidan,
                    'no_telp'     => $request->no_telp,
                    'id_posyandu' => $request->id_posyandu,
                ]),
                'Kader'    => Kader::create([
                    'id_user'     => $user->id_user,
                    'nama_kader'  => $request->nama_kader,
                    'wilayah'     => $request->wilayah,
                    'id_posyandu' => $request->id_posyandu,
                ]),
                'OrangTua' => OrangTua::create([
                    'nik_orang_tua' => $request->nik_orang_tua,
                    'id_user'       => $user->id_user,
                    'nama_ibu'      => $request->nama_ibu,
                    'alamat'        => $request->alamat,
                ]),
            };

            // Auto-daftarkan ke pivot posyandu
            if ($request->id_posyandu) {
                $user->posyanduList()->attach($request->id_posyandu);
            }
        });

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Form edit pengguna
     */
    public function edit($id)
    {
        $pengguna     = Pengguna::with(['bidan', 'kader', 'orangTua', 'posyanduList'])
            ->findOrFail($id);
        $posyanduList = Posyandu::where('status', 'Aktif')->get();

        return view('superadmin.pengguna.form', compact('pengguna', 'posyanduList'));
    }

    /**
     * Update pengguna
     */
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'username'    => 'sometimes|string|unique:pengguna,username,' . $id . ',id_user',
            'password'    => 'nullable|string|min:6',
            'id_posyandu' => 'nullable|exists:posyandu,id_posyandu',
            'nama_bidan'  => 'sometimes|string',
            'no_telp'     => 'nullable|string',
            'nama_kader'  => 'sometimes|string',
            'wilayah'     => 'nullable|string',
            'nama_ibu'    => 'sometimes|string',
            'alamat'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $pengguna) {
            $data = [];
            if ($request->filled('username'))   $data['username']    = $request->username;
            if ($request->filled('password'))   $data['password']    = Hash::make($request->password);
            if ($request->has('id_posyandu'))   $data['id_posyandu'] = $request->id_posyandu;
            $pengguna->update($data);

            if ($pengguna->isBidan() && $pengguna->bidan) {
                $pengguna->bidan->update(array_filter([
                    'nama_bidan'  => $request->nama_bidan,
                    'no_telp'     => $request->no_telp,
                    'id_posyandu' => $request->id_posyandu,
                ]));
            } elseif ($pengguna->isKader() && $pengguna->kader) {
                $pengguna->kader->update(array_filter([
                    'nama_kader'  => $request->nama_kader,
                    'wilayah'     => $request->wilayah,
                    'id_posyandu' => $request->id_posyandu,
                ]));
            } elseif ($pengguna->isOrangTua() && $pengguna->orangTua) {
                $pengguna->orangTua->update(array_filter([
                    'nama_ibu' => $request->nama_ibu,
                    'alamat'   => $request->alamat,
                ]));
            }
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
     * BARU: Assign posyandu tambahan ke pengguna
     * POST /superadmin/pengguna/{id}/assign-posyandu
     */
    public function assignPosyandu(Request $request, $id)
    {
        $request->validate([
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
        ]);

        $pengguna = Pengguna::with('posyanduList')->findOrFail($id);
        $posyandu = Posyandu::findOrFail($request->id_posyandu);

        // Cek sudah ada
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
            "Akses {$posyandu->nama_posyandu} berhasil ditambahkan ke {$pengguna->username}."
        );
    }

    /**
     * BARU: Cabut akses posyandu dari pengguna
     * DELETE /superadmin/pengguna/{id}/remove-posyandu
     */
    public function removePosyandu(Request $request, $id)
    {
        $request->validate([
            'id_posyandu' => 'required|exists:posyandu,id_posyandu',
        ]);

        $pengguna = Pengguna::findOrFail($id);
        $posyandu = Posyandu::findOrFail($request->id_posyandu);

        // Jangan hapus satu-satunya posyandu
        $total = $pengguna->posyanduList()->count();
        if ($total <= 1) {
            return back()->with('error',
                'Tidak dapat menghapus posyandu satu-satunya milik pengguna ini.'
            );
        }

        $pengguna->posyanduList()->detach($request->id_posyandu);

        // Reset posyandu aktif jika yang dihapus adalah yang aktif
        if ($pengguna->id_posyandu_aktif == $request->id_posyandu) {
            $pengguna->update(['id_posyandu_aktif' => $pengguna->id_posyandu]);
        }

        return back()->with('success',
            "Akses {$posyandu->nama_posyandu} berhasil dicabut dari {$pengguna->username}."
        );
    }
}