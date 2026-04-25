<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\Pemeriksaan;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posyandu'    => Posyandu::count(),
            'total_pengguna'    => Pengguna::whereIn('role', ['Bidan', 'Kader', 'OrangTua'])->count(),
            'total_bidan'       => Pengguna::where('role', 'Bidan')->count(),
            'total_kader'       => Pengguna::where('role', 'Kader')->count(),
            'total_anak'        => Anak::count(),
            'total_pemeriksaan' => Pemeriksaan::count(), 
            'total_imunisasi'   => Imunisasi::count(),
        ];

        $countPosyandu = Posyandu::count();
        $countUser = Pengguna::whereIn('role', ['Bidan', 'Kader'])->count();

        $posyanduList = Posyandu::all()->map(function ($p) {
            return [
                'id_posyandu'       => $p->id_posyandu,
                'nama_posyandu'     => $p->nama_posyandu,
                'wilayah'           => $p->wilayah,
                'total_kader'       => Pengguna::where('id_posyandu', $p->id_posyandu)->where('role', 'Kader')->count(),
                'total_bidan'       => Pengguna::where('id_posyandu', $p->id_posyandu)->where('role', 'Bidan')->count(),
                'pemeriksaan_bulan' => 0,
            ];
        });

        return view('superadmin.dashboard.index', compact('stats', 'posyanduList', 'countPosyandu', 'countUser'));
    }
}