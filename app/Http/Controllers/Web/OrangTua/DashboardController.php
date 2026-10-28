<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Identitas anak dari session login Orang Tua.
        $nikAnak = $request->session()->get('orang_tua_nik_anak');

        if (!$nikAnak) {
            return redirect('/login')->withErrors([
                'login' => 'Silakan login terlebih dahulu.',
            ]);
        }

        // Profil anak dan Posyandu tempat anak terdaftar.
        $anak = Anak::with('posyandu')
            ->where('nik_anak', $nikAnak)
            ->first();

        if (!$anak) {
            $request->session()->forget('orang_tua_nik_anak');

            return redirect('/login')->withErrors([
                'login' => 'Data anak tidak ditemukan. Silakan login kembali.',
            ]);
        }

        $hariIni = Carbon::today();

        // Pemeriksaan terakhir anak.
        $pemeriksaanTerakhir = DB::table('pemeriksaan')
            ->where('nik_anak', $anak->nik_anak)
            ->orderByDesc('tgl_periksa')
            ->orderByDesc('id_periksa')
            ->first();

        // Kalender kegiatan Posyandu bulan berjalan.
        $awalBulan = $hariIni->copy()->startOfMonth();
        $akhirBulan = $hariIni->copy()->endOfMonth();

        $jadwalPosyanduBulanIni = DB::table('jadwal_posyandu')
            ->where('id_posyandu', $anak->id_posyandu)
            ->whereBetween('tgl_kegiatan', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->orderBy('tgl_kegiatan')
            ->orderBy('waktu')
            ->get();

        // Kegiatan Posyandu tepat tiga hari dari hari ini.
        $tanggalTarget = $hariIni->copy()->addDays(3);

        $jadwalTerdekat = DB::table('jadwal_posyandu')
            ->where('id_posyandu', $anak->id_posyandu)
            ->whereDate('tgl_kegiatan', $tanggalTarget->toDateString())
            ->orderBy('waktu')
            ->orderBy('id_jadwal')
            ->get();

        /*
         * Jadwal imunisasi otomatis.
         * Tanggal target dihitung dari tanggal lahir anak dan
         * usia pemberian yang tersimpan di jadwal_imunisasi.
         */

        $jadwalAcuan = DB::table('jadwal_imunisasi')
            ->join(
                'jenis_vaksin',
                'jadwal_imunisasi.id_vaksin',
                '=',
                'jenis_vaksin.id_vaksin'
            )
            ->select(
                'jadwal_imunisasi.id_jadwal_imunisasi',
                'jadwal_imunisasi.id_vaksin',
                'jadwal_imunisasi.urutan_dosis',
                'jadwal_imunisasi.usia_hari',
                'jadwal_imunisasi.usia_bulan',
                'jadwal_imunisasi.khusus_wilayah_endemis',
                'jadwal_imunisasi.catatan',
                'jenis_vaksin.nama_vaksin'
            )
            ->orderByRaw(
                'COALESCE(jadwal_imunisasi.usia_hari, 0) +
                COALESCE(jadwal_imunisasi.usia_bulan, 0) * 30'
            )
            ->get();

        // Riwayat imunisasi yang benar-benar tercatat.
        $riwayatImunisasi = DB::table('imunisasi')
            ->where('nik_anak', $anak->nik_anak)
            ->orderBy('tgl_pemberian')
            ->get();

        $riwayatDenganDosis = $riwayatImunisasi
            ->filter(fn($item) => $item->urutan_dosis !== null);

        $riwayatTanpaDosis = $riwayatImunisasi
            ->filter(fn($item) => $item->urutan_dosis === null);

        // Hitung target dan status setiap dosis.
        $semuaJadwalImunisasi = $jadwalAcuan
            ->map(function ($jadwal) use ($anak, $hariIni, $riwayatDenganDosis, $riwayatTanpaDosis) {
                $tanggalLahir = Carbon::parse($anak->tgl_lahir);

                if ($jadwal->usia_hari !== null) {
                    $tanggalJadwal = $tanggalLahir
                        ->copy()
                        ->addDays((int) $jadwal->usia_hari);
                } elseif ($jadwal->usia_bulan !== null) {
                    $tanggalJadwal = $tanggalLahir
                        ->copy()
                        ->addMonthsNoOverflow(
                            (int) $jadwal->usia_bulan
                        );
                } else {
                    // Acuan tanpa target usia tidak dapat dijadwalkan.
                    return null;
                }

                $dosis = (int) $jadwal->urutan_dosis;
                $idVaksin = (int) $jadwal->id_vaksin;

                // Cari riwayat yang cocok dengan jenis dan nomor dosis.
                $sudahDiberikan = $riwayatDenganDosis->contains(
                    fn($riwayat) =>
                        (int) $riwayat->id_vaksin === $idVaksin
                        && (int) $riwayat->urutan_dosis === $dosis
                );

                // Riwayat tanpa nomor dosis perlu diverifikasi.
                $perluVerifikasi = $riwayatTanpaDosis->contains(
                    fn($riwayat) =>
                        (int) $riwayat->id_vaksin === $idVaksin
                );

                if ($sudahDiberikan) {
                    $status = 'Sudah tercatat diberikan';
                } elseif ($perluVerifikasi) {
                    $status = 'Perlu verifikasi riwayat';
                } elseif ($tanggalJadwal->lt($hariIni)) {
                    $status = 'Perlu tindak lanjut';
                } elseif ($tanggalJadwal->isSameDay($hariIni)) {
                    $status = 'Target hari ini';
                } else {
                    $status = 'Belum jatuh tempo';
                }

                return (object) [
                    'id_jadwal_imunisasi' =>
                        $jadwal->id_jadwal_imunisasi,
                    'id_vaksin' => $idVaksin,
                    'nama_vaksin' => $jadwal->nama_vaksin,
                    'urutan_dosis' => $dosis,
                    'tanggal_jadwal' => $tanggalJadwal,
                    'khusus_wilayah_endemis' =>
                        (bool) $jadwal->khusus_wilayah_endemis,
                    'catatan' => $jadwal->catatan,
                    'status' => $status,
                ];
            })
            ->filter()
            ->values();

        /*
         * Jadwal bulan berjalan ditambah semua target yang terlewat.
         * Dengan demikian, jadwal yang terlewat tidak menghilang
         * hanya karena tanggal targetnya berada di bulan sebelumnya.
         */
        $jadwalImunisasiBulanIni = $semuaJadwalImunisasi
            ->filter(function ($jadwal) use ($awalBulan, $akhirBulan, $hariIni) {
                $tanggal = $jadwal->tanggal_jadwal;

                $dalamBulanIni = $tanggal->between(
                    $awalBulan,
                    $akhirBulan,
                    true
                );

                $terlewat = $jadwal->status === 'Perlu tindak lanjut'
                    || $jadwal->status === 'Perlu verifikasi riwayat';

                return $dalamBulanIni || $terlewat;
            })
            ->sortBy('tanggal_jadwal')
            ->values();

        // Target berikutnya yang belum jatuh tempo atau target hari ini.
        $jadwalImunisasiTerdekat = $semuaJadwalImunisasi
            ->filter(
                fn($jadwal) =>
                    in_array($jadwal->status, [
                        'Target hari ini',
                        'Belum jatuh tempo',
                    ], true)
            )
            ->sortBy('tanggal_jadwal')
            ->take(1)
            ->values();

        // Prediksi stunting belum diambil karena mekanismenya
        // belum dipastikan dalam controller ini.
        $prediksiStunting = null;

        return view('orang_tua.dashboard.index', compact(
            'anak',
            'pemeriksaanTerakhir',
            'jadwalPosyanduBulanIni',
            'jadwalTerdekat',
            'tanggalTarget',
            'jadwalImunisasiBulanIni',
            'jadwalImunisasiTerdekat',
            'prediksiStunting'
        ));
    }
}
