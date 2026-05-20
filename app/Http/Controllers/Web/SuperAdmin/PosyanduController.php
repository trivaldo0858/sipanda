<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $posyandu = Posyandu::withCount([
            'anak',
            'penggunaKader as kader_count',
            'penggunaBidan as bidan_count',
        ])
        ->when(
            $request->search,
            fn($q) => $q->where('nama_posyandu', 'like', '%' . $request->search . '%')
        )
        ->paginate(10)
        ->withQueryString();

        return view('superadmin.posyandu.index', compact('posyandu'));
    }

    public function create()
    {
        return view('superadmin.posyandu.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_posyandu'  => 'required|string|max:100',
            'kecamatan'      => 'required|string|max:100',
            'desa_kelurahan' => 'required|string|max:100',
            'alamat'         => 'required|string',
            'kabupaten_kota' => 'nullable|string|max:100',
            'password_kader' => 'required|string|min:6',
        ]);

        Posyandu::create([
            'nama_posyandu'  => $request->nama_posyandu,
            'kecamatan'      => $request->kecamatan,
            'desa_kelurahan' => $request->desa_kelurahan,
            'alamat'         => $request->alamat,
            'kabupaten_kota' => $request->kabupaten_kota ?? 'Indramayu',
            'password_kader' => Hash::make($request->password_kader),
        ]);

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $posyandu = Posyandu::findOrFail($id);
        return view('superadmin.posyandu.form', compact('posyandu'));
    }

    public function update(Request $request, $id)
    {
        $posyandu = Posyandu::findOrFail($id);

        $request->validate([
            'nama_posyandu'  => 'required|string|max:100',
            'kecamatan'      => 'required|string|max:100',
            'desa_kelurahan' => 'required|string|max:100',
            'alamat'         => 'required|string',
            'kabupaten_kota' => 'nullable|string|max:100',
            'password_kader' => 'nullable|string|min:6',
        ]);

        $data = [
            'nama_posyandu'  => $request->nama_posyandu,
            'kecamatan'      => $request->kecamatan,
            'desa_kelurahan' => $request->desa_kelurahan,
            'alamat'         => $request->alamat,
            'kabupaten_kota' => $request->kabupaten_kota ?? 'Indramayu',
        ];

        if ($request->filled('password_kader')) {
            $data['password_kader'] = Hash::make($request->password_kader);
        }

        $posyandu->update($data);

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $posyandu = Posyandu::findOrFail($id);

        if ($posyandu->anak()->count() > 0) {
            return redirect()->route('superadmin.posyandu.index')
                ->with('error', 'Tidak dapat menghapus posyandu yang masih memiliki data balita.');
        }

        $posyandu->delete();

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu berhasil dihapus.');
    }
}