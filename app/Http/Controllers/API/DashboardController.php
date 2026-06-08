<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\JadwalPosyandu;
use App\Models\OrangTua;
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
        $totalAnak = Pemeriksaan::where('id_posyandu', $idPosyandu)->distinct('nik_anak')->count('nik_anak');
        $pemeriksaanBulanIni = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->whereMonth('tgl_periksa', now()->month)
            ->whereYear('tgl_periksa', now()->year)
            ->count();

        $menungguValidasi = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->where('status_validasi', 'Menunggu')
            ->count();

        $jadwalTerdekat = JadwalPosyandu::where('id_posyandu', $idPosyandu)
            ->where('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->first();

        $aktivitasTerbaru = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->with(['anak'])
            ->orderBy('tgl_periksa', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'nik_anak'        => $p->nik_anak,
                'nama_anak'       => $p->anak->nama_anak ?? '-',
                'tgl_periksa'     => $p->tgl_periksa->format('Y-m-d H:i:s'),
                'berat_badan'     => $p->berat_badan,
                'tinggi_badan'    => $p->tinggi_badan,
                'status_validasi' => $p->status_validasi,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'role'                  => 'Kader',
                'nama_posyandu'         => $user->posyandu?->nama_posyandu,
                'total_balita'          => $totalAnak,
                'pemeriksaan_bulan_ini' => $pemeriksaanBulanIni,
                'menunggu_validasi'     => $menungguValidasi,
                'jadwal_terdekat'       => $jadwalTerdekat ? [
                    'id_jadwal'    => $jadwalTerdekat->id_jadwal,
                    'tgl_kegiatan' => $jadwalTerdekat->tgl_kegiatan->format('Y-m-d'),
                    'lokasi'       => $jadwalTerdekat->lokasi,
                    'agenda'       => $jadwalTerdekat->agenda,
                ] : null,
                'aktivitas_terbaru' => $aktivitasTerbaru,
            ],
        ]);
    }

    // ── Dashboard Bidan ───────────────────────────────────────────────
    private function dashboardBidan($user)
    {
        $idPosyandu = $user->getPosyanduAktifId();
        $nip        = $user->bidan?->nip;
        $totalAnak = Pemeriksaan::where('id_posyandu', $idPosyandu)->distinct('nik_anak')->count('nik_anak');
        $totalPemeriksaan = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->whereMonth('tgl_periksa', now()->month)
            ->whereYear('tgl_periksa', now()->year)
            ->count();

        $totalImunisasi = $nip
            ? Imunisasi::where('nip_bidan', $nip)
                ->whereMonth('tgl_pemberian', now()->month)
                ->whereYear('tgl_pemberian', now()->year)
                ->count()
            : 0;

        $menungguValidasi = Pemeriksaan::where('id_posyandu', $idPosyandu)
            ->where('status_validasi', 'Menunggu')
            ->count();

        $jadwalTerdekat = JadwalPosyandu::where('id_posyandu', $idPosyandu)
            ->where('tgl_kegiatan', '>=', today())
            ->orderBy('tgl_kegiatan')
            ->first();

        $aktivitasImunisasi = $nip
            ? Imunisasi::where('nip_bidan', $nip)
                ->with(['anak:nik_anak,nama_anak', 'jenisVaksin:id_vaksin,nama_vaksin'])
                ->orderBy('tgl_pemberian', 'desc')
                ->take(5)
                ->get()
                ->map(fn ($i) => [
                    'nik_anak'      => $i->nik_anak,
                    'nama_anak'     => $i->anak?->nama_anak ?? '-',
                    'nama_vaksin'   => $i->jenisVaksin?->nama_vaksin ?? '-',
                    'tgl_pemberian' => $i->tgl_pemberian->format('Y-m-d H:i:s'),
                ])
            : [];

        return response()->json([
            'success' => true,
            'data'    => [
                'role'                          => 'Bidan',
                'nama_posyandu'                 => $user->posyandu?->nama_posyandu,
                'profil_bidan'                  => $user->bidan ? [
                    'nip'        => $user->bidan->nip,
                    'nama_bidan' => $user->bidan->nama_bidan,
                ] : null,
                'total_anak'                    => $totalAnak,
                'total_pemeriksaan_bulan'       => $totalPemeriksaan,
                'balita_perlu_imunisasi'        => $totalImunisasi,
                'imunisasi_bulan_ini'           => $totalImunisasi,
                'pemeriksaan_menunggu_validasi' => $menungguValidasi,
                'jadwal_terdekat'               => $jadwalTerdekat ? [
                    'id_jadwal'    => $jadwalTerdekat->id_jadwal,
                    'tgl_kegiatan' => $jadwalTerdekat->tgl_kegiatan->format('Y-m-d'),
                    'lokasi'       => $jadwalTerdekat->lokasi,
                ] : null,
                'aktivitas_imunisasi' => $aktivitasImunisasi,
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
                'pemeriksaan' => fn ($q) => $q
                    ->where('status_validasi', 'Disetujui')
                    ->latest('tgl_periksa')
                    ->take(1),
            ])
            ->get()
            ->map(fn ($a) => [
                'nik_anak'        => $a->nik_anak,
                'nama_anak'       => $a->nama_anak,
                'tgl_lahir'       => $a->tgl_lahir->format('Y-m-d'),
                'jenis_kelamin'   => $a->jenis_kelamin,
                'umur_bulan'      => $a->umur_bulan,
                'umur_format'     => $a->umur_format,
                'berat_terakhir'  => $a->pemeriksaan->first()?->berat_badan,
                'tinggi_terakhir' => $a->pemeriksaan->first()?->tinggi_badan,
                'lingkar_terakhir'=> $a->pemeriksaan->first()?->lingkar_kepala,
            ]);

        // Jadwal terdekat via posyandu orang tua
        $idPosyandu     = $orangTua->id_posyandu;
        $jadwalTerdekat = $idPosyandu
            ? JadwalPosyandu::where('id_posyandu', $idPosyandu)
                ->where('tgl_kegiatan', '>=', today())
                ->orderBy('tgl_kegiatan')
                ->first()
            : null;

        $notifBelumBaca = $user->notifikasi()
            ->where('status', 'Belum Dibaca')
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'role'             => 'OrangTua',
                'nama_ibu'         => $orangTua->nama_ibu,
                'daftar_anak'      => $anakList,
                'jadwal_terdekat'  => $jadwalTerdekat ? [
                    'id_jadwal'    => $jadwalTerdekat->id_jadwal,
                    'tgl_kegiatan' => $jadwalTerdekat->tgl_kegiatan->format('Y-m-d'),
                    'lokasi'       => $jadwalTerdekat->lokasi,
                    'agenda'       => $jadwalTerdekat->agenda,
                ] : null,
                'notifikasi_unread' => $notifBelumBaca,
            ],
        ]);
    }
}