<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\Pemeriksaan;
use App\Models\Pengguna;
use App\Models\Posyandu;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posyandu' => Posyandu::where('status', 'Aktif')->count(),
            'total_pengguna' => Pengguna::whereIn('role', ['Bidan', 'Kader', 'OrangTua'])->count(),
            'total_bidan' => Pengguna::where('role', 'Bidan')->count(),
            'total_kader' => Pengguna::where('role', 'Kader')->count(),
            'total_anak' => Anak::count(),
            'total_pemeriksaan' => Pemeriksaan::whereMonth('tgl_pemeriksaan', now()->month)->count(),
            'total_imunisasi' => Imunisasi::whereMonth('tgl_pemberian', now()->month)->count(),
        ];

        $countPosyandu = \App\Models\Posyandu::count();
        $countUser = \App\Models\User::where('role', 'Bidan')->count() + \App\Models\User::where('role', 'Kader')->count();


        $posyanduList = Posyandu::withCount(['kader', 'bidan'])
            ->with([
                'kader.pemeriksaan' => function ($q) {
                    $q->whereMonth('tgl_pemeriksaan', now()->month);
                }
            ])
            ->where('status', 'Aktif')
            ->get()
            ->map(fn($p) => [
                'id_posyandu' => $p->id_posyandu,
                'nama_posyandu' => $p->nama_posyandu,
                'wilayah' => $p->wilayah,
                'total_kader' => $p->kader_count,
                'total_bidan' => $p->bidan_count,
                'pemeriksaan_bulan' => $p->kader->flatMap->pemeriksaan->count(),
            ]);

        return view('superadmin.dashboard.index', compact('stats', 'posyanduList', 'countPosyandu', 'countUser'));
    }
}