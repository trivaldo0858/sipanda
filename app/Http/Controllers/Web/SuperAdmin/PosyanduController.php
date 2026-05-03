<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Penting untuk enkripsi password

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data posyandu dengan hitungan relasi
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
        // 1. Validasi input (Wajib mengisi password_kader untuk unit baru)
        $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'kecamatan' => 'required|string',
            'desa_kelurahan' => 'required|string',
            'alamat' => 'required|string',
            'password_kader' => 'required|min:6', // Syarat minimal 6 karakter
        ]);

        // 2. Simpan ke database
        Posyandu::create([
            'nama_posyandu' => $request->nama_posyandu,
            'kecamatan' => $request->kecamatan,
            'desa_kelurahan' => $request->desa_kelurahan,
            'alamat' => $request->alamat,
            'kabupaten_kota' => 'Indramayu',
            // Enkripsi password agar aman di database
            'password_kader' => bcrypt($request->password_kader),
        ]);

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', $request->nama_posyandu . ' berhasil didaftarkan.');
    }

    public function edit($id)
    {
        $posyandu = Posyandu::findOrFail($id);
        return view('superadmin.posyandu.form', compact('posyandu'));
    }

    public function update(Request $request, $id)
    {
        $posyandu = Posyandu::findOrFail($id);

        // 1. Validasi update (Password bersifat opsional saat edit)
        $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'kecamatan' => 'required|string',
            'desa_kelurahan' => 'required|string',
            'alamat' => 'required|string',
            'password_kader' => 'nullable|min:6', // Isi jika ingin ganti password saja
        ]);

        // 2. Siapkan data yang akan diupdate
        $data = [
            'nama_posyandu' => $request->nama_posyandu,
            'kecamatan' => $request->kecamatan,
            'desa_kelurahan' => $request->desa_kelurahan,
            'alamat' => $request->alamat,
        ];

        // 3. Logika ganti password: Hanya update jika diisi oleh Super Admin
        if ($request->filled('password_kader')) {
            $data['password_kader'] = Hash::make($request->password_kader);
        }

        $posyandu->update($data);

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Data Posyandu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Posyandu::findOrFail($id)->delete();

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu berhasil dihapus.');
    }
}