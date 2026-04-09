<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\Laporan;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Daftar laporan yang dibuat bidan
     */
    public function index(Request $request)
    {
        $query = Laporan::with('bidan');

        // Bidan hanya bisa lihat laporannya sendiri
        if ($request->user()->isBidan()) {
            $query->where('nip_bidan', $request->user()->bidan->nip);
        }

        if ($request->filled('jenis_laporan')) {
            $query->where('jenis_laporan', $request->jenis_laporan);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('tgl_cetak', 'desc')->paginate(15),
        ]);
    }

    /**
     * Generate & simpan laporan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:Bulanan,Tahunan',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $nip = $request->user()->bidan->nip;

        $laporan = Laporan::create([
            'nip_bidan'     => $nip,
            'jenis_laporan' => $request->jenis_laporan,
            'periode_awal'  => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'tgl_cetak'     => today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat.',
            'data'    => array_merge(
                $laporan->load('bidan')->toArray(),
                ['ringkasan' => $this->getRingkasan($request->periode_awal, $request->periode_akhir)]
            ),
        ], 201);
    }

    /**
     * Detail laporan + data ringkasan untuk cetak
     */
    public function show($id)
    {
        $laporan = Laporan::with('bidan')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => array_merge(
                $laporan->toArray(),
                ['ringkasan' => $this->getRingkasan($laporan->periode_awal, $laporan->periode_akhir)]
            ),
        ]);
    }

    public function destroy($id)
    {
        Laporan::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus.']);
    }

    // ----------------------------------------------------------------
    // Helper: ambil data ringkasan untuk periode tertentu
    // ----------------------------------------------------------------
    private function getRingkasan($periodeAwal, $periodeAkhir): array
    {
        $pemeriksaan = Pemeriksaan::whereBetween('tgl_pemeriksaan', [$periodeAwal, $periodeAkhir])
            ->with('anak')
            ->get();

        $imunisasi = Imunisasi::whereBetween('tgl_pemberian', [$periodeAwal, $periodeAkhir])
            ->with(['anak', 'jenisVaksin'])
            ->get();

        return [
            'total_pemeriksaan'     => $pemeriksaan->count(),
            'total_anak_diperiksa'  => $pemeriksaan->pluck('nik_anak')->unique()->count(),
            'total_imunisasi'       => $imunisasi->count(),
            'imunisasi_per_vaksin'  => $imunisasi->groupBy('jenisVaksin.nama_vaksin')
                                            ->map->count(),
            'rata_berat_badan'      => round($pemeriksaan->whereNotNull('berat_badan')->avg('berat_badan'), 2),
            'rata_tinggi_badan'     => round($pemeriksaan->whereNotNull('tinggi_badan')->avg('tinggi_badan'), 2),
        ];
    }
}