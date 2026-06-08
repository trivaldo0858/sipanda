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
     * List laporan — filter by posyandu aktif
     */
    public function index(Request $request)
    {
        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();

        $query = Laporan::with(['posyandu', 'bidan'])
            ->where('id_posyandu', $idPosyandu);

        if ($request->filled('jenis_laporan')) {
            $query->where('jenis_laporan', $request->jenis_laporan);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('tgl_cetak', 'desc')->paginate(15),
        ]);
    }

    /**
     * KF-010: Generate laporan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:Pemeriksaan,Imunisasi,Gabungan',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $user       = $request->user();
        $idPosyandu = $user->getPosyanduAktifId();
        $nipBidan   = $user->isBidan() ? $user->bidan?->nip : null;

        $laporan = Laporan::create([
            'id_posyandu'   => $idPosyandu,
            'nip_bidan'     => $nipBidan,
            'jenis_laporan' => $request->jenis_laporan,
            'periode_awal'  => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'tgl_cetak'     => today(),
        ]);

        $ringkasan = $this->getRingkasan(
            $request->jenis_laporan,
            $request->periode_awal,
            $request->periode_akhir,
            $idPosyandu
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat.',
            'data'    => array_merge(
                $laporan->load(['posyandu', 'bidan'])->toArray(),
                ['ringkasan' => $ringkasan]
            ),
        ], 201);
    }

    /**
     * Detail laporan + ringkasan data
     */
    public function show(Request $request, $id)
    {
        $laporan    = Laporan::with(['posyandu', 'bidan'])->findOrFail($id);
        $idPosyandu = $request->user()->getPosyanduAktifId();

        $ringkasan = $this->getRingkasan(
            $laporan->jenis_laporan,
            $laporan->periode_awal,
            $laporan->periode_akhir,
            $laporan->id_posyandu
        );

        return response()->json([
            'success' => true,
            'data'    => array_merge(
                $laporan->toArray(),
                ['ringkasan' => $ringkasan]
            ),
        ]);
    }

    /**
     * Hapus laporan
     */
    public function destroy($id)
    {
        Laporan::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus.',
        ]);
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request, $id)
    {
        $laporan   = Laporan::with(['posyandu', 'bidan'])->findOrFail($id);
        $ringkasan = $this->getRingkasan(
            $laporan->jenis_laporan,
            $laporan->periode_awal,
            $laporan->periode_akhir,
            $laporan->id_posyandu
        );
        $detail = $this->getDetail(
            $laporan->jenis_laporan,
            $laporan->periode_awal,
            $laporan->periode_akhir,
            $laporan->id_posyandu
        );

        $pdf = Pdf::loadView('laporan.pdf', compact('laporan', 'ringkasan', 'detail'))
            ->setPaper('a4', 'portrait');

        $namaFile = "Laporan_{$laporan->jenis_laporan}_{$laporan->periode_awal->format('Y-m')}.pdf";

        return $pdf->download($namaFile);
    }

    /**
     * Export Excel
     */
    public function exportExcel($id)
    {
        $laporan  = Laporan::with(['posyandu', 'bidan'])->findOrFail($id);
        $namaFile = "Laporan_{$laporan->jenis_laporan}_{$laporan->periode_awal->format('Y-m')}.xlsx";

        return Excel::download(new LaporanExport($laporan), $namaFile);
    }

    // ── Helper: ringkasan statistik ───────────────────────────────────
    private function getRingkasan(
        string $jenis,
        $periodeAwal,
        $periodeAkhir,
        ?int $idPosyandu
    ): array {
        $ringkasan = [];

        if (in_array($jenis, ['Pemeriksaan', 'Gabungan'])) {
            $pemeriksaan = Pemeriksaan::whereBetween('tgl_periksa', [$periodeAwal, $periodeAkhir])
                ->when($idPosyandu, fn ($q) => $q->where('id_posyandu', $idPosyandu))
                ->get();

            $ringkasan['pemeriksaan'] = [
                'total'             => $pemeriksaan->count(),
                'total_anak'        => $pemeriksaan->pluck('nik_anak')->unique()->count(),
                'rata_berat_badan'  => round($pemeriksaan->whereNotNull('berat_badan')->avg('berat_badan'), 2),
                'rata_tinggi_badan' => round($pemeriksaan->whereNotNull('tinggi_badan')->avg('tinggi_badan'), 2),
                'disetujui'         => $pemeriksaan->where('status_validasi', 'Disetujui')->count(),
                'menunggu'          => $pemeriksaan->where('status_validasi', 'Menunggu')->count(),
                'ditolak'           => $pemeriksaan->where('status_validasi', 'Ditolak')->count(),
            ];
        }

        if (in_array($jenis, ['Imunisasi', 'Gabungan'])) {
            $nikAnakPosyandu = \App\Models\Anak::whereHas('orangTua.pengguna', fn($q) => $q->where('id_posyandu', $idPosyandu))->pluck('nik_anak');
            $imunisasi = Imunisasi::whereBetween('tgl_pemberian', [$periodeAwal, $periodeAkhir])
                ->whereIn('nik_anak', $nikAnakPosyandu)
                ->with('jenisVaksin')
                ->get();

            $ringkasan['imunisasi'] = [
                'total'              => $imunisasi->count(),
                'total_anak'         => $imunisasi->pluck('nik_anak')->unique()->count(),
                'per_vaksin'         => $imunisasi
                    ->groupBy('jenisVaksin.nama_vaksin')
                    ->map->count(),
            ];
        }

        return $ringkasan;
    }

    // ── Helper: detail data untuk tabel laporan ───────────────────────
    private function getDetail(
        string $jenis,
        $periodeAwal,
        $periodeAkhir,
        ?int $idPosyandu
    ): array {
        $detail = [];

        if (in_array($jenis, ['Pemeriksaan', 'Gabungan'])) {
            $detail['pemeriksaan'] = Pemeriksaan::whereBetween('tgl_periksa', [$periodeAwal, $periodeAkhir])
                ->with(['anak.orangTua', 'bidan'])
                ->orderBy('tgl_periksa')
                ->get()
                ->map(fn ($p) => [
                    'nama_anak'      => $p->anak->nama_anak ?? '-',
                    'nama_ibu'       => $p->anak->orangTua->nama_ibu ?? '-',
                    'tgl_periksa'    => $p->tgl_periksa->format('d/m/Y'),
                    'berat_badan'    => $p->berat_badan ? $p->berat_badan . ' kg' : '-',
                    'tinggi_badan'   => $p->tinggi_badan ? $p->tinggi_badan . ' cm' : '-',
                    'lingkar_kepala' => $p->lingkar_kepala ? $p->lingkar_kepala . ' cm' : '-',
                    'keluhan'        => $p->keluhan ?? '-',
                    'status'         => $p->status_validasi,
                    'nama_bidan'     => $p->bidan?->nama_bidan ?? '-',
                ]);
        }

        if (in_array($jenis, ['Imunisasi', 'Gabungan'])) {
            $nikAnakPosyandu2 = \App\Models\Anak::whereHas('orangTua.pengguna', fn($q) => $q->where('id_posyandu', $idPosyandu))->pluck('nik_anak');
            $detail['imunisasi'] = Imunisasi::whereBetween('tgl_pemberian', [$periodeAwal, $periodeAkhir])
                ->whereIn('nik_anak', $nikAnakPosyandu2)
                ->with(['anak.orangTua', 'jenisVaksin', 'bidan'])
                ->orderBy('tgl_pemberian')
                ->get()
                ->map(fn ($i) => [
                    'nama_anak'     => $i->anak->nama_anak ?? '-',
                    'nama_ibu'      => $i->anak->orangTua->nama_ibu ?? '-',
                    'nama_vaksin'   => $i->jenisVaksin->nama_vaksin ?? '-',
                    'tgl_pemberian' => $i->tgl_pemberian->format('d/m/Y'),
                    'nama_bidan'    => $i->bidan?->nama_bidan ?? '-',
                    'catatan'       => $i->catatan ?? '-',
                ]);
        }

        return $detail;
    }
}