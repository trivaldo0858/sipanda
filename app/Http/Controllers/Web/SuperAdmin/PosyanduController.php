<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use App\Models\Pengguna;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $wilayah = Posyandu::query()
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $posyandu = Posyandu::query()

            // SEARCH
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(
                    'nama_posyandu',
                    'like',
                    '%' . $request->search . '%'
                );
            })

            // FILTER WILAYAH
            ->when($request->filled('wilayah'), function ($query) use ($request) {
                $query->where(
                    'kecamatan',
                    $request->wilayah
                );
            })

            ->orderBy('nama_posyandu')
            ->paginate(10)
            ->withQueryString();

        return view(
            'superadmin.posyandu.index',
            compact('posyandu', 'wilayah')
        );
    }

    public function create()
    {
        return view('superadmin.posyandu.form');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_posyandu' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'desa_kelurahan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'kabupaten_kota' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'username' => 'required|string|max:255|unique:pengguna,username',
            'password' => 'required|string|min:6',
        ]);

        $posyandu = Posyandu::create([
            'nama_posyandu' => $validated['nama_posyandu'],
            'kecamatan' => $validated['kecamatan'],
            'desa_kelurahan' => $validated['desa_kelurahan'],
            'alamat' => $validated['alamat'],
            'kabupaten_kota' => $validated['kabupaten_kota'] ?? 'Indramayu',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        Pengguna::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => 'Posyandu',
            'id_posyandu' => $posyandu->id_posyandu,
            'id_posyandu_aktif' => $posyandu->id_posyandu,
        ]);

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Posyandu dan akun akses berhasil ditambahkan.');
    }



    public function show($id)
    {
        $posyandu = Posyandu::with('bidan')
            ->withCount('anak')
            ->findOrFail($id);

        $akunAkses = Pengguna::where(
            'id_posyandu',
            $posyandu->id_posyandu
        )
            ->where('role', 'Posyandu')
            ->first();

        return view(
            'superadmin.posyandu.show',
            compact('posyandu', 'akunAkses')
        );
    }
    public function edit($id)
    {
        $posyandu = Posyandu::findOrFail($id);
        return view('superadmin.posyandu.form', compact('posyandu'));
    }
    public function update(Request $request, $id)
    {
        $posyandu = Posyandu::findOrFail($id);

        $validated = $request->validate([
            'nama_posyandu' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'desa_kelurahan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'kabupaten_kota' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'password' => 'nullable|string|min:6',
        ]);

        $posyandu->update([
            'nama_posyandu' => $validated['nama_posyandu'],
            'kecamatan' => $validated['kecamatan'],
            'desa_kelurahan' => $validated['desa_kelurahan'],
            'alamat' => $validated['alamat'],
            'kabupaten_kota' => $validated['kabupaten_kota'] ?? 'Indramayu',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        if ($request->filled('password')) {
            $pengguna = $posyandu->penggunaAkses;

            if ($pengguna) {
                $pengguna->update([
                    'password' => $validated['password'],
                ]);
            }
        }

        return redirect()->route('superadmin.posyandu.index')
            ->with('success', 'Data Posyandu berhasil diperbarui.');
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