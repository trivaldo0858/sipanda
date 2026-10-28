<?php

namespace App\Http\Controllers\Web\Bidan;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = $request->user();

        // Pastikan hanya akun Bidan yang dapat membuka dashboard ini.
        abort_unless($pengguna && $pengguna->isBidan(), 403);

        // Ambil profil Bidan yang terhubung dengan akun login.
        $bidan = $pengguna->bidan;

        abort_unless($bidan, 403, 'Profil Bidan belum terhubung dengan akun.');

        // Batasi data sesuai Posyandu tempat Bidan bertugas.
        $idPosyandu = $bidan->id_posyandu;

        $stats = [
            'total_anak' => Anak::where(
                'id_posyandu',
                $idPosyandu
            )->count(),

            'total_pemeriksaan' => Pemeriksaan::where(
                'id_posyandu',
                $idPosyandu
            )->count(),
        ];

        $pemeriksaanTerbaru = Pemeriksaan::with('anak')
            ->where('id_posyandu', $idPosyandu)
            ->orderByDesc('tgl_periksa')
            ->limit(5)
            ->get();

        return view('bidan.dashboard.index', compact(
            'bidan',
            'stats',
            'pemeriksaanTerbaru'
        ));
    }
}
