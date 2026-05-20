<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\JadwalPosyandu;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'Kader'    => $this->dashboardKader($user),
            'Bidan'    => $this->dashboardBidan($user),
            'OrangTua' => $this->dashboardOrangTua($user),
            default    => response()->json(['success' => false, 'message' => 'Role tidak dikenal.'], 403),
        };
    }

    // ── Dashboard Kader ───────────────────────────────────────────────
    private function dashboardKader($user)
    {
        $idPosyandu = $user->getPosyanduAktifId();

        $totalAnak = Anak::where('id_posyandu', $idPosyandu)->count();

        $pemeriksaanBulanIni = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->whereMonth('tgl_periksa', now()->month)
            ->whereYear('tgl_periksa', now()->year)
            ->count();

        $menungguValidasi = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->where('status_validasi', 'Menunggu')
            ->count();

        $jadwalMendatang = JadwalPosyandu::where('id_posyandu', $idPosyandu)
            ->where('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->take(3)
            ->get();

        $pemeriksaanTerbaru = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->with(['anak'])
            ->orderBy('tgl_periksa', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id_periksa'     => $p->id_periksa,
                'nama_anak'      => $p->anak->nama_anak ?? '-',
                'tgl_periksa'    => $p->tgl_periksa->format('d/m/Y'),
                'berat_badan'    => $p->berat_badan,
                'status_validasi'=> $p->status_validasi,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'role'                    => 'Kader',
                'posyandu'                => $user->posyandu ? [
                    'id_posyandu'   => $user->posyandu->id_posyandu,
                    'nama_posyandu' => $user->posyandu->nama_posyandu,
                ] : null,
                'total_anak'              => $totalAnak,
                'pemeriksaan_bulan_ini'   => $pemeriksaanBulanIni,
                'menunggu_validasi'        => $menungguValidasi,
                'jadwal_mendatang'        => $jadwalMendatang,
                'pemeriksaan_terbaru'     => $pemeriksaanTerbaru,
            ],
        ]);
    }

    // ── Dashboard Bidan ───────────────────────────────────────────────
    private function dashboardBidan($user)
    {
        $idPosyandu = $user->getPosyanduAktifId();

        $totalAnak = Anak::where('id_posyandu', $idPosyandu)->count();

        $totalPemeriksaan = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->whereMonth('tgl_periksa', now()->month)
            ->whereYear('tgl_periksa', now()->year)
            ->count();

        $totalImunisasi = Imunisasi::where('id_posyandu', $idPosyandu)
            ->whereMonth('tgl_pemberian', now()->month)
            ->whereYear('tgl_pemberian', now()->year)
            ->count();

        $menungguValidasi = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->where('status_validasi', 'Menunggu')
            ->count();

        $jadwalMendatang = JadwalPosyandu::where('id_posyandu', $idPosyandu)
            ->where('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->take(3)
            ->get();

        $pemeriksaanMenunggu = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->where('status_validasi', 'Menunggu')
            ->with('anak')
            ->orderBy('tgl_periksa', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id_periksa'  => $p->id_periksa,
                'nama_anak'   => $p->anak->nama_anak ?? '-',
                'tgl_periksa' => $p->tgl_periksa->format('d/m/Y'),
                'berat_badan' => $p->berat_badan,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'role'                    => 'Bidan',
                'posyandu'                => $user->posyandu ? [
                    'id_posyandu'   => $user->posyandu->id_posyandu,
                    'nama_posyandu' => $user->posyandu->nama_posyandu,
                ] : null,
                'profil_bidan'            => $user->bidan ? [
                    'nip'        => $user->bidan->nip,
                    'nama_bidan' => $user->bidan->nama_bidan,
                ] : null,
                'total_anak'              => $totalAnak,
                'total_pemeriksaan_bulan' => $totalPemeriksaan,
                'total_imunisasi_bulan'   => $totalImunisasi,
                'menunggu_validasi'        => $menungguValidasi,
                'jadwal_mendatang'        => $jadwalMendatang,
                'pemeriksaan_menunggu'    => $pemeriksaanMenunggu,
            ],
        ]);
    }

    // ── Dashboard Orang Tua ───────────────────────────────────────────
    private function dashboardOrangTua($user)
    {
        $orangTua = $user->orangTua;

        if (! $orangTua) {
            return response()->json([
                'success' => false,
                'message' => 'Data orang tua tidak ditemukan.',
            ], 404);
        }

        $anakList = $orangTua->anak()
            ->with([
                'posyandu:id_posyandu,nama_posyandu',
                'pemeriksaan' => fn ($q) => $q
                    ->where('status_validasi', 'Disetujui')
                    ->latest('tgl_periksa')
                    ->take(1),
                'imunisasi' => fn ($q) => $q
                    ->latest('tgl_pemberian')
                    ->take(1)
                    ->with('jenisVaksin'),
            ])
            ->get()
            ->map(fn ($a) => [
                'nik_anak'        => $a->nik_anak,
                'nama_anak'       => $a->nama_anak,
                'tgl_lahir'       => $a->tgl_lahir->format('Y-m-d'),
                'jenis_kelamin'   => $a->jenis_kelamin,
                'umur_bulan'      => $a->umur_bulan,
                'umur_format'     => $a->umur_format,
                'nama_posyandu'   => $a->posyandu?->nama_posyandu,
                'pemeriksaan_terakhir' => $a->pemeriksaan->first() ? [
                    'tgl_periksa'  => $a->pemeriksaan->first()->tgl_periksa->format('d/m/Y'),
                    'berat_badan'  => $a->pemeriksaan->first()->berat_badan,
                    'tinggi_badan' => $a->pemeriksaan->first()->tinggi_badan,
                ] : null,
                'imunisasi_terakhir' => $a->imunisasi->first() ? [
                    'nama_vaksin'   => $a->imunisasi->first()->jenisVaksin?->nama_vaksin,
                    'tgl_pemberian' => $a->imunisasi->first()->tgl_pemberian->format('d/m/Y'),
                ] : null,
            ]);

        // Jadwal mendatang dari posyandu anak-anaknya
        $posyanduIds = $orangTua->anak->pluck('id_posyandu')->unique();
        $jadwalMendatang = JadwalPosyandu::whereIn('id_posyandu', $posyanduIds)
            ->where('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->take(3)
            ->with('posyandu:id_posyandu,nama_posyandu')
            ->get();

        $notifBelumBaca = $user->notifikasi()
            ->where('status', 'Belum Dibaca')
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'role'             => 'OrangTua',
                'nama_ibu'         => $orangTua->nama_ibu,
                'anak'             => $anakList,
                'jadwal_mendatang' => $jadwalMendatang,
                'notif_belum_baca' => $notifBelumBaca,
            ],
        ]);
    }
}