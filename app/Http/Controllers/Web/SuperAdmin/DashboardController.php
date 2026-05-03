<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Posyandu;
use App\Models\Bidan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_anak' => Anak::count(),
            'total_posyandu' => Posyandu::count(), // Total Unit Posyandu
            'total_bidan' => Bidan::count(),    // Total Bidan
        ];

        $posyanduList = Posyandu::all()->map(function ($p) {
            return [
                'id_posyandu' => $p->id_posyandu,
                'nama_posyandu' => $p->nama_posyandu,
                'kecamatan' => $p->kecamatan,
                'desa_kelurahan' => $p->desa_kelurahan,
            ];
        });

        // Mengambil rata-rata berat badan 6 bulan terakhir secara dinamis
        $pemeriksaanData = \App\Models\Pemeriksaan::select(
            \Illuminate\Support\Facades\DB::raw('MONTHNAME(tgl_periksa) as bulan'),
            \Illuminate\Support\Facades\DB::raw('AVG(berat_badan) as rata_rata')
        )
            ->groupBy('bulan')
            ->orderBy('tgl_periksa', 'ASC')
            ->limit(6)
            ->get();

        // Format data untuk Chart.js
        $chartData = [
            'labels' => $pemeriksaanData->pluck('bulan')->toArray(),
            'data' => $pemeriksaanData->pluck('rata_rata')->toArray(),
        ];

        return view('superadmin.dashboard.index', compact('stats', 'posyanduList', 'chartData'));
    }
}