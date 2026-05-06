<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Posyandu;
use App\Models\Bidan;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mengambil statistik ringkasan global
        $stats = [
            'total_anak' => Anak::count(),
            'total_posyandu' => Posyandu::count(),
            'total_bidan' => Bidan::count(),
            // Tambahkan ini agar variabel $stats['total_pemeriksaan'] di view tidak error
            'total_pemeriksaan' => Pemeriksaan::count(),
            'total_imunisasi' => 0, // Sesuaikan jika ada model Imunisasi
            'total_pengguna' => 0,  // Sesuaikan jika ada model User
            'total_kader' => 0,
        ];

        // 2. Mengambil daftar posyandu untuk dropdown filter
        $posyanduList = Posyandu::select('id_posyandu', 'nama_posyandu')->get();

        // 3. Menangani Logika Filter (Global vs Per Unit)
        $selectedPosyandu = $request->query('id_posyandu');

        // PERBAIKAN DI SINI: Ganti tgl_periksa menjadi tgl_pemeriksaan
        $query = Pemeriksaan::select(
            DB::raw('MONTHNAME(tgl_periksa) as bulan'),
            DB::raw('AVG(berat_badan) as rata_bb'),
            DB::raw('AVG(tinggi_badan) as rata_tb'),
            DB::raw('MIN(tgl_periksa) as tgl_sort')
        );

        if ($selectedPosyandu && $selectedPosyandu !== 'global') {
            $query->whereHas('anak', function ($q) use ($selectedPosyandu) {
                $q->where('id_posyandu', $selectedPosyandu);
            });
        }

        $pemeriksaanData = $query->groupBy('bulan')
            ->orderBy('tgl_sort', 'ASC')
            ->limit(6)
            ->get();

        // 4. Format data untuk Chart.js
        $chartData = [
            'labels' => $pemeriksaanData->pluck('bulan')->toArray(),
            'dataBB' => $pemeriksaanData->pluck('rata_bb')->map(fn($val) => round($val, 2))->toArray(),
            'dataTB' => $pemeriksaanData->pluck('rata_tb')->map(fn($val) => round($val, 2))->toArray(),
        ];

        return view('superadmin.dashboard.index', compact('stats', 'posyanduList', 'chartData', 'selectedPosyandu'));
    }
}