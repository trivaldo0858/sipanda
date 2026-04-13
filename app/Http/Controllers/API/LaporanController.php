<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Imunisasi;
use App\Models\Laporan;
use App\Models\Pemeriksaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    /**
     * Daftar laporan
     */
    public function index(Request $request)
    {
        $query = Laporan::with('bidan');

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
     * Detail laporan
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

    /**
     * Hapus laporan
     */
    public function destroy($id)
    {
        Laporan::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus.']);
    }

    // ----------------------------------------------------------------
    // KF-010: Export PDF
    // GET /api/v1/laporan/{id}/export-pdf
    // ----------------------------------------------------------------
    public function exportPdf($id)
    {
        $laporan   = Laporan::with('bidan')->findOrFail($id);
        $ringkasan = $this->getRingkasan($laporan->periode_awal, $laporan->periode_akhir);
        $detail    = $this->getDetailPemeriksaan($laporan->periode_awal, $laporan->periode_akhir);

        $pdf = Pdf::loadView('laporan.pdf', [
            'laporan'   => $laporan,
            'ringkasan' => $ringkasan,
            'detail'    => $detail,
        ])->setPaper('a4', 'portrait');

        $namaFile = 'Laporan_' . $laporan->jenis_laporan . '_'
            . $laporan->periode_awal->format('Y-m') . '.pdf';

        return $pdf->download($namaFile);
    }

    // ----------------------------------------------------------------
    // KF-010: Export Excel
    // GET /api/v1/laporan/{id}/export-excel
    // ----------------------------------------------------------------
    public function exportExcel($id)
    {
        $laporan = Laporan::with('bidan')->findOrFail($id);

        $namaFile = 'Laporan_' . $laporan->jenis_laporan . '_'
            . $laporan->periode_awal->format('Y-m') . '.xlsx';

        return Excel::download(
            new LaporanExport($laporan),
            $namaFile
        );
    }

    // ----------------------------------------------------------------
    // Helper: ringkasan statistik
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
            'total_pemeriksaan'    => $pemeriksaan->count(),
            'total_anak_diperiksa' => $pemeriksaan->pluck('nik_anak')->unique()->count(),
            'total_imunisasi'      => $imunisasi->count(),
            'imunisasi_per_vaksin' => $imunisasi->groupBy('jenisVaksin.nama_vaksin')->map->count(),
            'rata_berat_badan'     => round($pemeriksaan->whereNotNull('berat_badan')->avg('berat_badan'), 2),
            'rata_tinggi_badan'    => round($pemeriksaan->whereNotNull('tinggi_badan')->avg('tinggi_badan'), 2),
        ];
    }

    // ----------------------------------------------------------------
    // Helper: detail data pemeriksaan untuk tabel di PDF/Excel
    // ----------------------------------------------------------------
    private function getDetailPemeriksaan($periodeAwal, $periodeAkhir): \Illuminate\Support\Collection
    {
        return Pemeriksaan::whereBetween('tgl_pemeriksaan', [$periodeAwal, $periodeAkhir])
            ->with(['anak.orangTua', 'kader', 'bidan'])
            ->orderBy('tgl_pemeriksaan')
            ->get()
            ->map(fn ($p) => [
                'nama_anak'       => $p->anak->nama_anak ?? '-',
                'nama_ibu'        => $p->anak->orangTua->nama_ibu ?? '-',
                'tgl_pemeriksaan' => $p->tgl_pemeriksaan->format('d/m/Y'),
                'berat_badan'     => $p->berat_badan ? $p->berat_badan . ' kg' : '-',
                'tinggi_badan'    => $p->tinggi_badan ? $p->tinggi_badan . ' cm' : '-',
                'lingkar_kepala'  => $p->lingkar_kepala ? $p->lingkar_kepala . ' cm' : '-',
                'keluhan'         => $p->keluhan ?? '-',
                'nama_kader'      => $p->kader->nama_kader ?? '-',
                'nama_bidan'      => $p->bidan->nama_bidan ?? '-',
            ]);
    }
}