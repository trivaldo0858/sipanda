<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $posyandu = Posyandu::withCount(['kader', 'bidan'])
            ->when(
                $request->search,
                fn($q) =>
                $q->where('nama_posyandu', 'like', '%' . $request->search . '%')
            )
            ->paginate(10);

        return view('superadmin.posyandu.index', compact('posyandu'));
    }

    public function create()
    {
        return view('superadmin.posyandu.form');
    }

    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            'nama_posyandu' => 'required',
            'kecamatan' => 'required',
            'desa_kelurahan' => 'required',
            'alamat' => 'required',
            // tambahkan field lain jika ada
        ]);

        // 2. Simpan ke database
        \App\Models\Posyandu::create([
            'nama_posyandu' => $request->nama_posyandu,
            'kecamatan' => $request->kecamatan, // Tambahkan baris ini
            'desa_kelurahan' => $request->desa_kelurahan, // Tambahkan baris ini
            'alamat' => $request->alamat,
            'kabupaten_kota' => 'Indramayu', // Karena kita set tetap
        ]);

        return redirect()->route('superadmin.posyandu.index')->with('success', 'Unit Berhasil Ditambahkan');
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
            'nama_posyandu' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'wilayah' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
            'status' => 'in:Aktif,Tidak Aktif',
        ]);

        $posyandu->update($request->only(['nama_posyandu', 'alamat', 'wilayah', 'no_telp', 'status']));

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Posyandu::findOrFail($id)->delete();

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu berhasil dihapus.');
    }
}