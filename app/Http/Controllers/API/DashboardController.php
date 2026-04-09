<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\JadwalPosyandu;
use App\Models\Pemeriksaan;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'Bidan'    => $this->dashboardBidan(),
            'Kader'    => $this->dashboardKader($user),
            'OrangTua' => $this->dashboardOrangTua($user),
        };
    }

    // ----------------------------------------------------------------
    private function dashboardBidan(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_anak'              => Anak::count(),
                'total_pemeriksaan_bulan' => Pemeriksaan::whereMonth('tgl_pemeriksaan', now()->month)
                                                ->whereYear('tgl_pemeriksaan', now()->year)->count(),
                'total_imunisasi_bulan'   => Imunisasi::whereMonth('tgl_pemberian', now()->month)
                                                ->whereYear('tgl_pemberian', now()->year)->count(),
                'total_pengguna'          => Pengguna::count(),
                'jadwal_mendatang'        => JadwalPosyandu::where('tgl_kegiatan', '>=', today())
                                                ->orderBy('tgl_kegiatan')->take(5)->with('kader')->get(),
                'pemeriksaan_terbaru'     => Pemeriksaan::with(['anak', 'kader'])
                                                ->orderBy('tgl_pemeriksaan', 'desc')->take(5)->get(),
            ],
        ]);
    }

    private function dashboardKader(Pengguna $user): \Illuminate\Http\JsonResponse
    {
        $kader = $user->kader;

        return response()->json([
            'success' => true,
            'data' => [
                'total_anak'              => Anak::count(),
                'jadwal_saya'             => JadwalPosyandu::where('id_kader', $kader->id_kader)
                                                ->where('tgl_kegiatan', '>=', today())
                                                ->orderBy('tgl_kegiatan')->take(5)->get(),
                'pemeriksaan_bulan_ini'   => Pemeriksaan::where('id_kader', $kader->id_kader)
                                                ->whereMonth('tgl_pemeriksaan', now()->month)
                                                ->whereYear('tgl_pemeriksaan', now()->year)->count(),
                'pemeriksaan_terbaru'     => Pemeriksaan::where('id_kader', $kader->id_kader)
                                                ->with('anak')->orderBy('tgl_pemeriksaan', 'desc')->take(5)->get(),
            ],
        ]);
    }

    private function dashboardOrangTua(Pengguna $user): \Illuminate\Http\JsonResponse
    {
        $nikOrangTua = $user->orangTua->nik_orang_tua;

        $anakList = Anak::where('nik_orang_tua', $nikOrangTua)
            ->with([
                'pemeriksaan' => fn ($q) => $q->latest('tgl_pemeriksaan')->take(1),
                'imunisasi'   => fn ($q) => $q->latest('tgl_pemberian')->take(1)->with('jenisVaksin'),
            ])->get()
            ->map(fn ($a) => array_merge($a->toArray(), ['umur_bulan' => $a->umur_bulan]));

        return response()->json([
            'success' => true,
            'data' => [
                'anak'             => $anakList,
                'jadwal_mendatang' => JadwalPosyandu::where('tgl_kegiatan', '>=', today())
                                        ->orderBy('tgl_kegiatan')->take(3)->with('kader')->get(),
                'notif_belum_baca' => $user->notifikasi()->where('status', 'Belum Dibaca')->count(),
            ],
        ]);
    }
}