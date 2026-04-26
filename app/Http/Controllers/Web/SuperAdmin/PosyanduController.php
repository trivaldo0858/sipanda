<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data posyandu untuk tabel utama
        $posyandu = Posyandu::withCount(['kader', 'bidan'])
            ->when(
                $request->search,
                fn($q) =>
                $q->where('nama_posyandu', 'like', '%' . $request->search . '%')
            )
            ->paginate(10);

        $summary = [
            'total_balita' => \App\Models\Anak::count(),
            'total_unit' => \App\Models\Posyandu::count(),
        ];

        // Kirimkan variabel $posyandu dan $summary ke view
        return view('superadmin.posyandu.index', compact('posyandu', 'summary'));
    }

    public function create()
    {
        // View form.blade.php yang kita buat sebelumnya
        return view('superadmin.posyandu.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_posyandu' => 'required|string|max:100',
            'alamat' => 'required|string', // Sesuai mockup, alamat wajib diisi
            'wilayah' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
        ]);

        Posyandu::create([
            'nama_posyandu' => $request->nama_posyandu,
            'alamat' => $request->alamat,
            'wilayah' => $request->wilayah,
            'no_telp' => $request->no_telp,
            'status' => 'Aktif', // Status default saat baru didaftarkan
        ]);

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Unit Posyandu baru berhasil didaftarkan.');
    }

    public function edit($id)
    {
        // Menggunakan findOrFail yang akan mencari id_posyandu (jika sudah diatur di Model)
        $posyandu = Posyandu::findOrFail($id);
        return view('superadmin.posyandu.form', compact('posyandu'));
    }

    public function update(Request $request, $id)
    {
        $posyandu = Posyandu::findOrFail($id);

        $request->validate([
            'nama_posyandu' => 'required|string|max:100',
            'alamat' => 'required|string',
            'wilayah' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $posyandu->update($request->all());

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Data unit posyandu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $posyandu = Posyandu::findOrFail($id);
        $posyandu->delete();

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Unit posyandu telah dihapus dari sistem.');
    }
}