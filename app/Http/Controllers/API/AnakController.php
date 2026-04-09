<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Illuminate\Http\Request;

class AnakController extends Controller
{
    /**
     * Daftar anak
     * - Kader/Bidan: semua anak
     * - OrangTua: hanya anak miliknya
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Anak::with('orangTua');

        if ($user->isOrangTua()) {
            $query->where('nik_orang_tua', $user->orangTua->nik_orang_tua);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_anak', 'like', '%' . $request->search . '%')
                  ->orWhere('nik_anak', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $query->paginate(15),
        ]);
    }

    /**
     * Tambah data anak
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik_anak'      => 'required|string|unique:anak,nik_anak',
            'nik_orang_tua' => 'required|exists:orang_tua,nik_orang_tua',
            'nama_anak'     => 'required|string|max:100',
            'tgl_lahir'     => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $anak = Anak::create($request->only([
            'nik_anak', 'nik_orang_tua', 'nama_anak', 'tgl_lahir', 'jenis_kelamin',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil ditambahkan.',
            'data'    => $anak->load('orangTua'),
        ], 201);
    }

    /**
     * Detail anak beserta riwayat pemeriksaan & imunisasi
     */
    public function show(Request $request, $nik)
    {
        $user = $request->user();
        $anak = Anak::with([
            'orangTua',
            'pemeriksaan.kader',
            'pemeriksaan.bidan',
            'imunisasi.jenisVaksin',
            'imunisasi.bidan',
        ])->findOrFail($nik);

        // OrangTua hanya bisa lihat anaknya sendiri
        if ($user->isOrangTua() && $anak->nik_orang_tua !== $user->orangTua->nik_orang_tua) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => array_merge($anak->toArray(), ['umur_bulan' => $anak->umur_bulan]),
        ]);
    }

    /**
     * Update data anak
     */
    public function update(Request $request, $nik)
    {
        $anak = Anak::findOrFail($nik);

        $request->validate([
            'nama_anak'     => 'sometimes|string|max:100',
            'tgl_lahir'     => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'nik_orang_tua' => 'sometimes|exists:orang_tua,nik_orang_tua',
        ]);

        $anak->update($request->only(['nama_anak', 'tgl_lahir', 'jenis_kelamin', 'nik_orang_tua']));

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil diperbarui.',
            'data'    => $anak->fresh('orangTua'),
        ]);
    }

    /**
     * Hapus data anak
     */
    public function destroy($nik)
    {
        $anak = Anak::findOrFail($nik);
        $anak->delete();

        return response()->json(['success' => true, 'message' => 'Data anak berhasil dihapus.']);
    }

    /**
     * Grafik perkembangan anak (berat, tinggi, lingkar kepala)
     */
    public function perkembangan(Request $request, $nik)
    {
        $user = $request->user();
        $anak = Anak::findOrFail($nik);

        if ($user->isOrangTua() && $anak->nik_orang_tua !== $user->orangTua->nik_orang_tua) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $pemeriksaan = $anak->pemeriksaan()
            ->select('tgl_pemeriksaan', 'berat_badan', 'tinggi_badan', 'lingkar_kepala')
            ->orderBy('tgl_pemeriksaan')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'anak'         => ['nik_anak' => $anak->nik_anak, 'nama_anak' => $anak->nama_anak],
                'pemeriksaan'  => $pemeriksaan,
            ],
        ]);
    }
}