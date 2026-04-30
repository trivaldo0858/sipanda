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
            'Bidan'    => $this->dashboardBidan($user),
            'Kader'    => $this->dashboardKader($user),
            'OrangTua' => $this->dashboardOrangTua($user),
            default    => response()->json(['success' => false, 'message' => 'Role tidak dikenal.'], 403),
        };
    }

    // ── Dashboard Bidan ───────────────────────────────────────────────
    private function dashboardBidan(Pengguna $user)
    {
        // Filter berdasarkan posyandu AKTIF
        $idPosyandu = $user->getPosyanduAktifId();

        $totalAnak = Anak::when($idPosyandu, fn ($q) =>
            $q->whereHas('orangTua.pengguna', fn ($q2) =>
                $q2->where('id_posyandu', $idPosyandu)
            )
        )->count();

        $totalPemeriksaan = Pemeriksaan::whereMonth('tgl_pemeriksaan', now()->month)
            ->whereYear('tgl_pemeriksaan', now()->year)
            ->when($idPosyandu, fn ($q) =>
                $q->whereHas('kader', fn ($q2) =>
                    $q2->where('id_posyandu', $idPosyandu)
                )
            )
            ->count();

        $totalImunisasi = Imunisasi::whereMonth('tgl_pemberian', now()->month)
            ->whereYear('tgl_pemberian', now()->year)
            ->when($idPosyandu, fn ($q) =>
                $q->whereHas('anak.orangTua.pengguna', fn ($q2) =>
                    $q2->where('id_posyandu', $idPosyandu)
                )
            )
            ->count();

        $jadwalMendatang = JadwalPosyandu::where('tgl_kegiatan', '>=', today())
            ->when($idPosyandu, fn ($q) =>
                $q->whereHas('kader', fn ($q2) =>
                    $q2->where('id_posyandu', $idPosyandu)
                )
            )
            ->orderBy('tgl_kegiatan')
            ->take(5)
            ->with('kader')
            ->get();

        $pemeriksaanTerbaru = Pemeriksaan::when($idPosyandu, fn ($q) =>
            $q->whereHas('kader', fn ($q2) =>
                $q2->where('id_posyandu', $idPosyandu)
            )
        )
        ->with(['anak', 'kader'])
        ->orderBy('tgl_pemeriksaan', 'desc')
        ->take(5)
        ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'posyandu_aktif'         => $this->getPosyanduAktifData($user),
                'total_anak'             => $totalAnak,
                'total_pemeriksaan_bulan'=> $totalPemeriksaan,
                'total_imunisasi_bulan'  => $totalImunisasi,
                'jadwal_mendatang'       => $jadwalMendatang,
                'pemeriksaan_terbaru'    => $pemeriksaanTerbaru,
            ],
        ]);
    }

    // ── Dashboard Kader ───────────────────────────────────────────────
    private function dashboardKader(Pengguna $user)
    {
        $idPosyandu = $user->getPosyanduAktifId();
        $kader      = $user->kader;

        $totalAnak = Anak::when($idPosyandu, fn ($q) =>
            $q->whereHas('orangTua.pengguna', fn ($q2) =>
                $q2->where('id_posyandu', $idPosyandu)
            )
        )->count();

        $jadwalSaya = JadwalPosyandu::when($kader, fn ($q) =>
            $q->where('id_kader', $kader->id_kader)
        )
        ->where('tgl_kegiatan', '>=', today())
        ->orderBy('tgl_kegiatan')
        ->take(5)
        ->get();

        $pemeriksaanBulanIni = Pemeriksaan::when($kader, fn ($q) =>
            $q->where('id_kader', $kader->id_kader)
        )
        ->whereMonth('tgl_pemeriksaan', now()->month)
        ->whereYear('tgl_pemeriksaan', now()->year)
        ->count();

        $pemeriksaanTerbaru = Pemeriksaan::when($kader, fn ($q) =>
            $q->where('id_kader', $kader->id_kader)
        )
        ->with('anak')
        ->orderBy('tgl_pemeriksaan', 'desc')
        ->take(5)
        ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'posyandu_aktif'          => $this->getPosyanduAktifData($user),
                'total_anak'              => $totalAnak,
                'pemeriksaan_bulan_ini'   => $pemeriksaanBulanIni,
                'jadwal_saya'             => $jadwalSaya,
                'pemeriksaan_terbaru'     => $pemeriksaanTerbaru,
            ],
        ]);
    }

    // ── Dashboard Orang Tua ───────────────────────────────────────────
    private function dashboardOrangTua(Pengguna $user)
    {
        $nikOrangTua = $user->orangTua->nik_orang_tua;

        $anakList = Anak::where('nik_orang_tua', $nikOrangTua)
            ->with([
                'pemeriksaan' => fn ($q) => $q->latest('tgl_pemeriksaan')->take(1),
                'imunisasi'   => fn ($q) => $q->latest('tgl_pemberian')->take(1)->with('jenisVaksin'),
            ])
            ->get()
            ->map(fn ($a) => array_merge($a->toArray(), ['umur_bulan' => $a->umur_bulan]));

        $jadwalMendatang = JadwalPosyandu::where('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->take(3)
            ->with('kader')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'anak'             => $anakList,
                'jadwal_mendatang' => $jadwalMendatang,
                'notif_belum_baca' => $user->notifikasi()->where('status', 'Belum Dibaca')->count(),
            ],
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────
    private function getPosyanduAktifData(Pengguna $user): ?array
    {
        $id = $user->getPosyanduAktifId();
        if (! $id) return null;

        $p = \App\Models\Posyandu::find($id);
        if (! $p) return null;

        return [
            'id_posyandu'   => $p->id_posyandu,
            'nama_posyandu' => $p->nama_posyandu,
            'wilayah'       => $p->wilayah,
        ];
    }
}