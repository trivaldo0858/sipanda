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
        ];

        $posyanduMap = Posyandu::with('bidan')
            ->select(
                'id_posyandu',
                'nama_posyandu',
                'desa_kelurahan',
                'kecamatan',
                'alamat',
                'latitude',
                'longitude'
            )
            ->get();

        $lastMapSync = now();

        return view('superadmin.dashboard.index', compact(
            'stats',
            'posyanduMap',
            'lastMapSync'
        ));
    }
    public function mapData()
{
    $posyanduMap = Posyandu::with('bidan')
        ->select(
            'id_posyandu',
            'nama_posyandu',
            'desa_kelurahan',
            'kecamatan',
            'alamat',
            'latitude',
            'longitude'
        )
        ->get();

    return response()->json([
        'success' => true,
        'data' => $posyanduMap,
        'last_updated' => now()->toIso8601String(),
    ]);
}
}