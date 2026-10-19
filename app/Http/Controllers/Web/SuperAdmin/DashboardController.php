<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Posyandu;
use App\Models\Bidan;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_anak' => Anak::count(),
            'total_posyandu' => Posyandu::count(),
            'total_bidan' => Bidan::count(),
        ];

        $posyanduMap = $this->getPosyanduMapData();
        $lastMapSync = now();

        return view('superadmin.dashboard.index', compact(
            'stats',
            'posyanduMap',
            'lastMapSync'
        ));
    }

    public function mapData()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getPosyanduMapData(),
            'last_updated' => now()->toIso8601String(),
        ]);
    }

    private function getPosyanduMapData()
    {
        $posyanduList = Posyandu::with('bidan')
            ->withCount([
                'anak as anak_count',
                'pemeriksaan as pemeriksaan_disetujui_count' => function ($query) {
                    $query->where('status_validasi', 'Disetujui');
                },
            ])
            ->select([
                'id_posyandu',
                'nama_posyandu',
                'desa_kelurahan',
                'kecamatan',
                'alamat',
                'latitude',
                'longitude',
            ])
            ->get();

        return $posyanduList->map(function ($posyandu) {
            $posyandu->risk_level =
                $posyandu->pemeriksaan_disetujui_count > 0
                ? 'perlu_klasifikasi'
                : 'belum_ada_data';

            return $posyandu;
        });
    }
}