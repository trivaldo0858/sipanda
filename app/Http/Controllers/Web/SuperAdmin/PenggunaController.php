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
    public function index(Request $request)
    {
        $pengguna = Pengguna::with(['bidan', 'kader', 'orangTua', 'posyandu'])
            ->whereIn('role', ['Bidan', 'Kader', 'OrangTua'])
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->posyandu, fn ($q) => $q->where('id_posyandu', $request->posyandu))
            ->when($request->search, fn ($q) =>
                $q->where('username', 'like', '%' . $request->search . '%')
            )
            ->paginate(15);

        $posyanduList = Posyandu::where('status', 'Aktif')->get();

        return view('superadmin.pengguna.index', compact('pengguna', 'posyanduList'));
    }

    public function create()
    {
        $posyanduList = Posyandu::where('status', 'Aktif')->get();
        return view('superadmin.pengguna.form', compact('posyanduList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'      => 'required|string|unique:pengguna,username',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:Bidan,Kader,OrangTua',
            'id_posyandu'   => 'nullable|exists:posyandu,id_posyandu',
            // Bidan
            'nip'           => 'required_if:role,Bidan|string',
            'nama_bidan'    => 'required_if:role,Bidan|string',
            'no_telp'       => 'nullable|string',
            // Kader
            'nama_kader'    => 'required_if:role,Kader|string',
            'wilayah'       => 'nullable|string',
            // OrangTua
            'nik_orang_tua' => 'required_if:role,OrangTua|string',
            'nama_ibu'      => 'required_if:role,OrangTua|string',
            'alamat'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = Pengguna::create([
                'username'    => $request->username,
                'password'    => Hash::make($request->password),
                'role'        => $request->role,
                'id_posyandu' => $request->id_posyandu,
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
        });

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pengguna     = Pengguna::with(['bidan', 'kader', 'orangTua'])->findOrFail($id);
        $posyanduList = Posyandu::where('status', 'Aktif')->get();
        return view('superadmin.pengguna.form', compact('pengguna', 'posyanduList'));
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'username'    => 'sometimes|string|unique:pengguna,username,' . $id . ',id_user',
            'password'    => 'nullable|string|min:6',
            'id_posyandu' => 'nullable|exists:posyandu,id_posyandu',
        ]);

        DB::transaction(function () use ($request, $pengguna) {
            $data = ['id_posyandu' => $request->id_posyandu];
            if ($request->filled('username')) $data['username'] = $request->username;
            if ($request->filled('password')) $data['password'] = Hash::make($request->password);
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

    public function destroy($id)
    {
        Pengguna::findOrFail($id)->delete();

        return redirect()->route('superadmin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}