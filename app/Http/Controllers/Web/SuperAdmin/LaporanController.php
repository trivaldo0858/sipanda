<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Imunisasi;
use App\Models\Laporan;
use App\Models\Pemeriksaan;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Laporan lintas posyandu
     */
    public function index(Request $request)
    {
        $posyanduList = Posyandu::where('status', 'Aktif')->get();

        $query = Laporan::with(['bidan.posyandu'])
            ->when($request->id_posyandu, fn ($q) =>
                $q->whereHas('bidan', fn ($b) =>
                    $b->where('id_posyandu', $request->id_posyandu)
                )
            )
            ->when($request->jenis_laporan, fn ($q) =>
                $q->where('jenis_laporan', $request->jenis_laporan)
            );

        $laporan = $query->orderBy('tgl_cetak', 'desc')->paginate(15);

        return view('superadmin.laporan.index', compact('laporan', 'posyanduList'));
    }

    /**
     * Ringkasan global semua posyandu
     */
    public function globalSummary(Request $request)
    {
        $periodeAwal  = $request->get('periode_awal', now()->startOfMonth()->format('Y-m-d'));
        $periodeAkhir = $request->get('periode_akhir', now()->endOfMonth()->format('Y-m-d'));

        $posyanduList = Posyandu::where('status', 'Aktif')
            ->get()
            ->map(function ($p) use ($periodeAwal, $periodeAkhir) {
                $kaderIds = $p->kader->pluck('id_kader');

                $pemeriksaan = Pemeriksaan::whereIn('id_kader', $kaderIds)
                    ->whereBetween('tgl_pemeriksaan', [$periodeAwal, $periodeAkhir])
                    ->count();

                $imunisasi = Imunisasi::whereHas('anak.orangTua.pengguna', fn ($q) =>
                    $q->where('id_posyandu', $p->id_posyandu)
                )
                ->whereBetween('tgl_pemberian', [$periodeAwal, $periodeAkhir])
                ->count();

                return [
                    'nama_posyandu' => $p->nama_posyandu,
                    'wilayah'       => $p->wilayah,
                    'pemeriksaan'   => $pemeriksaan,
                    'imunisasi'     => $imunisasi,
                ];
            });

        return view('superadmin.laporan.global', compact('posyanduList', 'periodeAwal', 'periodeAkhir'));
    }
}