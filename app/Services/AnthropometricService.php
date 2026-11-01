<?php

namespace App\Services;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class AnthropometricService
{
    private const BATAS_USIA_HARI = 1826;

    /**
     * Analisis tinggi badan menurut umur (TB/U).
     */
    public function analyzeHeightForAge(
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): array {
        if ($pemeriksaan->nik_anak !== $anak->nik_anak) {
            throw new InvalidArgumentException(
                'Data pemeriksaan tidak sesuai dengan anak.'
            );
        }

        if (
            !$anak->tgl_lahir ||
            !$pemeriksaan->tgl_periksa
        ) {
            throw new InvalidArgumentException(
                'Tanggal lahir atau tanggal pemeriksaan belum tersedia.'
            );
        }

        if (!in_array($anak->jenis_kelamin, ['L', 'P'], true)) {
            throw new InvalidArgumentException(
                'Jenis kelamin anak tidak valid.'
            );
        }

        if (
            $pemeriksaan->tinggi_badan === null ||
            (float) $pemeriksaan->tinggi_badan <= 0
        ) {
            return [
                'tersedia' => false,
                'indikator' => 'TB_U',
                'nama_indikator' => 'Tinggi Badan Menurut Umur',
                'alasan' => 'Tinggi badan belum tersedia atau tidak valid.',
            ];
        }

        $tanggalLahir = CarbonImmutable::parse(
            $anak->tgl_lahir
        )->startOfDay();

        $tanggalPeriksa = CarbonImmutable::parse(
            $pemeriksaan->tgl_periksa
        )->startOfDay();

        if ($tanggalPeriksa->lessThan($tanggalLahir)) {
            throw new InvalidArgumentException(
                'Tanggal pemeriksaan tidak boleh sebelum tanggal lahir.'
            );
        }



        $interval = $tanggalLahir->diff($tanggalPeriksa);

        // Umur aktual berdasarkan selisih tanggal.
        $umurHariAktual = (int) $interval->days;

        // Umur bulan penuh sesuai Permenkes.
        $umurBulan = ($interval->y * 12) + $interval->m;

        // Standar WHO digunakan untuk anak usia sampai 60 bulan.
        if ($umurHariAktual > self::BATAS_USIA_HARI) {
            return [
                'tersedia' => false,
                'alasan' => 'Usia anak sudah melebihi 60 bulan.',
            ];
        }

        // Karena tanggal lahir dan pemeriksaan tersedia,
// gunakan umur aktual dalam hari untuk referensi LMS WHO.
        $umurHariReferensi = $umurHariAktual;

        // Ambil referensi TB/U berdasarkan umur aktual dan jenis kelamin.
        $referensi = DB::table('referensi_antropometri_who')
            ->where('indikator', 'TB_U')
            ->where('jenis_kelamin', $anak->jenis_kelamin)
            ->where('umur_hari', $umurHariReferensi)
            ->first();

        if (!$referensi) {
            throw new RuntimeException(
                'Referensi WHO TB/U untuk usia anak tidak ditemukan.'
            );
        }

        // Jenis acuan mengikuti penanda panjang/tinggi
// pada referensi WHO untuk umur tersebut.
        $jenisUkuran = $referensi->jenis_ukuran;


        $tinggiBadan = (float) $pemeriksaan->tinggi_badan;

        // Menghitung Z-score dengan koreksi SD23.
        $zScore = $this->calculateLmsZScore(
            $tinggiBadan,
            $referensi
        );

        /**
         * Kategori berdasarkan Permenkes No. 2 Tahun 2020.
         */
        if ($zScore < -3) {
            $kategoriBerdasarkanZ = 'Sangat pendek';
        } elseif ($zScore < -2) {
            $kategoriBerdasarkanZ = 'Pendek';
        } elseif ($zScore <= 3) {
            $kategoriBerdasarkanZ = 'Normal';
        } else {
            $kategoriBerdasarkanZ = 'Tinggi';
        }

        /**
         * Batas flag TB/U menurut WHO Anthro:
         * lebih kecil dari -6 SD atau lebih besar dari +6 SD.
         */
        $perluTinjauUlang = $zScore < -6 || $zScore > 6;

        $alasanTinjauUlang = null;

        if ($zScore < -6) {
            $alasanTinjauUlang = 'Z-score berada di bawah -6 SD.';
        } elseif ($zScore > 6) {
            $alasanTinjauUlang = 'Z-score berada di atas +6 SD.';
        }

        return [
            'tersedia' => true,

            'indikator' => 'TB_U',
            'nama_indikator' => 'Tinggi Badan Menurut Umur',

            'umur_hari' => $umurHariAktual,
            'umur_bulan' => $umurBulan,
            'umur_hari_referensi' => $umurHariReferensi,

            'jenis_kelamin' => $anak->jenis_kelamin,
            'jenis_ukuran' => $jenisUkuran,

            'tinggi_badan' => $tinggiBadan,

            'z_score' => round($zScore, 2),

            // Kategori sementara berdasarkan nilai Z-score.
            'kategori_berdasarkan_z_score' => $kategoriBerdasarkanZ,

            // Flag ekstrem dipisahkan dari kategori pertumbuhan.
            'nilai_ekstrem' => abs($zScore) > 3,

            // Hasil ekstrem di luar batas flag tidak langsung
            // ditampilkan sebagai kategori pertumbuhan.
            'kategori' => $perluTinjauUlang
                ? 'Perlu pemeriksaan ulang'
                : $kategoriBerdasarkanZ,

            // Null berarti status stunting belum layak ditetapkan.
            'status_stunting' => $perluTinjauUlang
                ? null
                : $zScore < -2,

            'perlu_tinjau_ulang' => $perluTinjauUlang,
            'alasan_tinjau_ulang' => $alasanTinjauUlang,

            'batas_flag' => [
                'minimum' => -6,
                'maksimum' => 6,
            ],
        ];
    }

    /**
     * Analisis berat badan menurut umur (BB/U).
     */
    public function analyzeWeightForAge(
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): array {
        if ($pemeriksaan->nik_anak !== $anak->nik_anak) {
            throw new InvalidArgumentException(
                'Data pemeriksaan tidak sesuai dengan anak.'
            );
        }

        if (
            !$anak->tgl_lahir ||
            !$pemeriksaan->tgl_periksa
        ) {
            throw new InvalidArgumentException(
                'Tanggal lahir atau tanggal pemeriksaan belum tersedia.'
            );
        }

        if (!in_array($anak->jenis_kelamin, ['L', 'P'], true)) {
            throw new InvalidArgumentException(
                'Jenis kelamin anak tidak valid.'
            );
        }

        if (
            $pemeriksaan->berat_badan === null ||
            (float) $pemeriksaan->berat_badan <= 0
        ) {
            return [
                'tersedia' => false,
                'alasan' => 'Berat badan belum tersedia atau tidak valid.',
            ];
        }

        $tanggalLahir = CarbonImmutable::parse(
            $anak->tgl_lahir
        )->startOfDay();

        $tanggalPeriksa = CarbonImmutable::parse(
            $pemeriksaan->tgl_periksa
        )->startOfDay();

        if ($tanggalPeriksa->lessThan($tanggalLahir)) {
            throw new InvalidArgumentException(
                'Tanggal pemeriksaan tidak boleh sebelum tanggal lahir.'
            );
        }

        $interval = $tanggalLahir->diff($tanggalPeriksa);

        // Umur aktual dalam hari.
        $umurHariAktual = (int) $interval->days;

        // Umur dalam bulan penuh.
        $umurBulan = ($interval->y * 12) + $interval->m;

        // Batas referensi WHO untuk anak usia sampai 60 bulan.
        if ($umurHariAktual > self::BATAS_USIA_HARI) {
            return [
                'tersedia' => false,
                'alasan' => 'Usia anak sudah melebihi rentang referensi WHO.',
            ];
        }

        // Referensi BB/U berdasarkan umur aktual dan jenis kelamin.
        $referensi = DB::table('referensi_antropometri_who')
            ->where('indikator', 'BB_U')
            ->where('jenis_kelamin', $anak->jenis_kelamin)
            ->where('umur_hari', $umurHariAktual)
            ->first();

        if (!$referensi) {
            throw new RuntimeException(
                'Referensi WHO BB/U tidak ditemukan.'
            );
        }

        $beratBadan = (float) $pemeriksaan->berat_badan;

        // Perhitungan LMS dengan koreksi SD23.
        $zScore = $this->calculateLmsZScore(
            $beratBadan,
            $referensi
        );

        // Klasifikasi menurut Permenkes No. 2 Tahun 2020.
        if ($zScore < -3) {
            $kategoriBerdasarkanZ = 'Berat badan sangat kurang';
        } elseif ($zScore < -2) {
            $kategoriBerdasarkanZ = 'Berat badan kurang';
        } elseif ($zScore <= 1) {
            $kategoriBerdasarkanZ = 'Berat badan normal';
        } else {
            $kategoriBerdasarkanZ = 'Risiko berat badan lebih';
        }

        // Batas flag WHO untuk WAZ: -6 sampai +5 SD.
        $perluTinjauUlang = $zScore < -6 || $zScore > 5;

        $alasanTinjauUlang = null;

        if ($zScore < -6) {
            $alasanTinjauUlang = 'Z-score berada di bawah -6 SD.';
        } elseif ($zScore > 5) {
            $alasanTinjauUlang = 'Z-score berada di atas +5 SD.';
        }

        return [
            'tersedia' => true,

            'indikator' => 'BB_U',
            'nama_indikator' => 'Berat Badan Menurut Umur',

            'umur_hari' => $umurHariAktual,
            'umur_bulan' => $umurBulan,

            'jenis_kelamin' => $anak->jenis_kelamin,
            'berat_badan' => $beratBadan,

            'z_score' => round($zScore, 2),

            'kategori_berdasarkan_z_score' => $kategoriBerdasarkanZ,

            'nilai_ekstrem' => abs($zScore) > 3,

            'kategori' => $perluTinjauUlang
                ? 'Perlu pemeriksaan ulang'
                : $kategoriBerdasarkanZ,

            'perlu_tinjau_ulang' => $perluTinjauUlang,
            'alasan_tinjau_ulang' => $alasanTinjauUlang,

            'batas_flag' => [
                'minimum' => -6,
                'maksimum' => 5,
            ],
        ];
    }


    /**
     * Analisis indeks massa tubuh menurut umur (IMT/U).
     */
    public function analyzeBmiForAge(
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): array {
        if ($pemeriksaan->nik_anak !== $anak->nik_anak) {
            throw new InvalidArgumentException(
                'Data pemeriksaan tidak sesuai dengan anak.'
            );
        }

        if (!$anak->tgl_lahir || !$pemeriksaan->tgl_periksa) {
            throw new InvalidArgumentException(
                'Tanggal lahir atau tanggal pemeriksaan belum tersedia.'
            );
        }

        if (!in_array($anak->jenis_kelamin, ['L', 'P'], true)) {
            throw new InvalidArgumentException(
                'Jenis kelamin anak tidak valid.'
            );
        }

        if (
            $pemeriksaan->berat_badan === null ||
            $pemeriksaan->tinggi_badan === null ||
            (float) $pemeriksaan->berat_badan <= 0 ||
            (float) $pemeriksaan->tinggi_badan <= 0
        ) {
            return [
                'tersedia' => false,
                'alasan' => 'Berat badan atau tinggi badan belum tersedia.',
            ];
        }

        $tanggalLahir = CarbonImmutable::parse(
            $anak->tgl_lahir
        )->startOfDay();

        $tanggalPeriksa = CarbonImmutable::parse(
            $pemeriksaan->tgl_periksa
        )->startOfDay();

        if ($tanggalPeriksa->lessThan($tanggalLahir)) {
            throw new InvalidArgumentException(
                'Tanggal pemeriksaan tidak boleh sebelum tanggal lahir.'
            );
        }

        $interval = $tanggalLahir->diff($tanggalPeriksa);

        $umurHari = (int) $interval->days;
        $umurBulan = ($interval->y * 12) + $interval->m;

        if ($umurHari > self::BATAS_USIA_HARI) {
            return [
                'tersedia' => false,
                'alasan' => 'Usia anak sudah melebihi rentang referensi WHO.',
            ];
        }

        $beratBadan = (float) $pemeriksaan->berat_badan;
        $tinggiBadan = (float) $pemeriksaan->tinggi_badan;

        // WHO menggunakan panjang badan sebelum 731 hari
        // dan tinggi badan mulai 731 hari.
        $jenisUkuran = $umurHari < 731 ? 'P' : 'T';

        // Pemeriksaan rentang pengukuran berdasarkan usia.
        $minimum = $jenisUkuran === 'P' ? 45.0 : 65.0;
        $maksimum = $jenisUkuran === 'P' ? 110.0 : 120.0;

        if (
            $tinggiBadan < $minimum ||
            $tinggiBadan > $maksimum
        ) {
            return [
                'tersedia' => false,
                'alasan' => "Pengukuran harus berada dalam rentang {$minimum} sampai {$maksimum} cm.",
            ];
        }

        // Menghitung IMT menggunakan kilogram dan meter.
        $imt = $beratBadan / pow($tinggiBadan / 100, 2);

        // Mengambil referensi LMS IMT/U sesuai usia dan jenis kelamin.
        $referensi = DB::table('referensi_antropometri_who')
            ->where('indikator', 'IMT_U')
            ->where('jenis_kelamin', $anak->jenis_kelamin)
            ->where('umur_hari', $umurHari)
            ->where('jenis_ukuran', $jenisUkuran)
            ->first();

        if (!$referensi) {
            throw new RuntimeException(
                'Referensi WHO IMT/U tidak ditemukan.'
            );
        }

        // Menghitung Z-score menggunakan LMS dan koreksi SD23.
        $zScore = $this->calculateLmsZScore(
            $imt,
            $referensi
        );

        // Klasifikasi menurut Permenkes No. 2 Tahun 2020.
        if ($zScore < -3) {
            $kategoriBerdasarkanZ = 'Gizi buruk';
        } elseif ($zScore < -2) {
            $kategoriBerdasarkanZ = 'Gizi kurang';
        } elseif ($zScore <= 1) {
            $kategoriBerdasarkanZ = 'Gizi baik';
        } elseif ($zScore <= 2) {
            $kategoriBerdasarkanZ = 'Berisiko gizi lebih';
        } elseif ($zScore <= 3) {
            $kategoriBerdasarkanZ = 'Gizi lebih';
        } else {
            $kategoriBerdasarkanZ = 'Obesitas';
        }

        // Batas flag WHO untuk BAZ: -5 sampai +5 SD.
        $perluTinjauUlang = $zScore < -5 || $zScore > 5;

        $alasanTinjauUlang = null;

        if ($zScore < -5) {
            $alasanTinjauUlang = 'Z-score berada di bawah -5 SD.';
        } elseif ($zScore > 5) {
            $alasanTinjauUlang = 'Z-score berada di atas +5 SD.';
        }

        return [
            'tersedia' => true,

            'indikator' => 'IMT_U',
            'nama_indikator' => 'Indeks Massa Tubuh Menurut Umur',

            'umur_hari' => $umurHari,
            'umur_bulan' => $umurBulan,

            'jenis_kelamin' => $anak->jenis_kelamin,
            'jenis_ukuran' => $jenisUkuran,

            'berat_badan' => $beratBadan,
            'tinggi_badan' => $tinggiBadan,
            'imt' => round($imt, 2),

            'z_score' => round($zScore, 2),

            'kategori_berdasarkan_z_score' => $kategoriBerdasarkanZ,

            'nilai_ekstrem' => abs($zScore) > 3,

            'kategori' => $perluTinjauUlang
                ? 'Perlu pemeriksaan ulang'
                : $kategoriBerdasarkanZ,

            'perlu_tinjau_ulang' => $perluTinjauUlang,
            'alasan_tinjau_ulang' => $alasanTinjauUlang,

            'batas_flag' => [
                'minimum' => -5,
                'maksimum' => 5,
            ],
        ];
    }

    /**
     * Analisis lingkar kepala menurut umur (LK/U).
     *
     * Menggunakan referensi LMS WHO berdasarkan umur
     * aktual dalam hari dan jenis kelamin anak.
     */
    public function analyzeHeadCircumferenceForAge(
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): array {
        if ($pemeriksaan->nik_anak !== $anak->nik_anak) {
            throw new InvalidArgumentException(
                'Data pemeriksaan tidak sesuai dengan anak.'
            );
        }

        if (!$anak->tgl_lahir || !$pemeriksaan->tgl_periksa) {
            throw new InvalidArgumentException(
                'Tanggal lahir atau pemeriksaan belum tersedia.'
            );
        }

        if (!in_array($anak->jenis_kelamin, ['L', 'P'], true)) {
            throw new InvalidArgumentException(
                'Jenis kelamin anak tidak valid.'
            );
        }

        if (
            $pemeriksaan->lingkar_kepala === null ||
            (float) $pemeriksaan->lingkar_kepala <= 0
        ) {
            return [
                'tersedia' => false,
                'alasan' => 'Lingkar kepala belum tersedia atau tidak valid.',
            ];
        }

        $tanggalLahir = CarbonImmutable::parse(
            $anak->tgl_lahir
        )->startOfDay();

        $tanggalPeriksa = CarbonImmutable::parse(
            $pemeriksaan->tgl_periksa
        )->startOfDay();

        if ($tanggalPeriksa->lessThan($tanggalLahir)) {
            throw new InvalidArgumentException(
                'Tanggal pemeriksaan tidak boleh sebelum tanggal lahir.'
            );
        }

        $interval = $tanggalLahir->diff($tanggalPeriksa);

        // Umur aktual berdasarkan selisih tanggal.
        $umurHari = (int) $interval->days;

        // Umur bulan penuh untuk informasi.
        $umurBulan = ($interval->y * 12) + $interval->m;

        // Batas penggunaan WHO Child Growth Standards.
        if ($umurHari > self::BATAS_USIA_HARI) {
            return [
                'tersedia' => false,
                'alasan' => 'Usia anak sudah melebihi rentang referensi WHO.',
            ];
        }

        $lingkarKepala = (float) $pemeriksaan->lingkar_kepala;

        // Ambil referensi LK/U berdasarkan umur dan jenis kelamin.
        $referensi = DB::table('referensi_antropometri_who')
            ->where('indikator', 'LK_U')
            ->where('jenis_kelamin', $anak->jenis_kelamin)
            ->where('umur_hari', $umurHari)
            ->first();

        if (!$referensi) {
            throw new RuntimeException(
                'Referensi WHO LK/U untuk umur anak tidak ditemukan.'
            );
        }

        // Hitung Z-score menggunakan parameter LMS.
        $zScore = $this->calculateLmsZScore(
            $lingkarKepala,
            $referensi
        );

        // Interpretasi deskriptif terhadap standar WHO.
        if ($zScore < -2) {
            $interpretasi = 'Di bawah rentang rujukan';
        } elseif ($zScore > 2) {
            $interpretasi = 'Di atas rentang rujukan';
        } else {
            $interpretasi = 'Dalam rentang rujukan';
        }

        // WHO Anthro memberikan flag jika Z-score
        // berada di bawah -5 SD atau di atas +5 SD.
        $perluTinjauUlang = $zScore < -5 || $zScore > 5;

        $alasanTinjauUlang = null;

        if ($zScore < -5) {
            $alasanTinjauUlang = 'Z-score berada di bawah -5 SD.';
        } elseif ($zScore > 5) {
            $alasanTinjauUlang = 'Z-score berada di atas +5 SD.';
        }

        return [
            'tersedia' => true,

            'indikator' => 'LK_U',
            'nama_indikator' => 'Lingkar Kepala Menurut Umur',

            'umur_hari' => $umurHari,
            'umur_bulan' => $umurBulan,

            'jenis_kelamin' => $anak->jenis_kelamin,

            'lingkar_kepala' => $lingkarKepala,

            'z_score' => round($zScore, 2),
            'interpretasi_berdasarkan_z_score' => $interpretasi,
            'interpretasi' => $perluTinjauUlang
                ? 'Perlu pemeriksaan ulang'
                : $interpretasi,
            'nilai_ekstrem' => abs($zScore) > 3,
            'perlu_tinjau_ulang' => $perluTinjauUlang,
            'alasan_tinjau_ulang' => $alasanTinjauUlang,
            'batas_flag' => [
                'minimum' => -5,
                'maksimum' => 5,
            ],
        ];
    }

    /**
     * Analisis berat badan menurut panjang/tinggi badan.
     */
    public function analyzeWeightForLengthHeight(
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): array {
        if ($pemeriksaan->nik_anak !== $anak->nik_anak) {
            throw new InvalidArgumentException(
                'Data pemeriksaan tidak sesuai dengan anak.'
            );
        }

        if (!$anak->tgl_lahir || !$pemeriksaan->tgl_periksa) {
            throw new InvalidArgumentException(
                'Tanggal lahir atau pemeriksaan belum tersedia.'
            );
        }

        if (!in_array($anak->jenis_kelamin, ['L', 'P'], true)) {
            throw new InvalidArgumentException(
                'Jenis kelamin anak tidak valid.'
            );
        }

        $beratBadan = (float) $pemeriksaan->berat_badan;
        $tinggiBadan = (float) $pemeriksaan->tinggi_badan;

        if (
            $pemeriksaan->berat_badan === null ||
            $pemeriksaan->tinggi_badan === null ||
            $beratBadan <= 0 ||
            $tinggiBadan <= 0
        ) {
            return [
                'tersedia' => false,
                'alasan' => 'Berat badan atau tinggi badan belum tersedia.',
            ];
        }

        $tanggalLahir = CarbonImmutable::parse(
            $anak->tgl_lahir
        )->startOfDay();

        $tanggalPeriksa = CarbonImmutable::parse(
            $pemeriksaan->tgl_periksa
        )->startOfDay();

        if ($tanggalPeriksa->lessThan($tanggalLahir)) {
            throw new InvalidArgumentException(
                'Tanggal pemeriksaan tidak boleh sebelum tanggal lahir.'
            );
        }

        $interval = $tanggalLahir->diff($tanggalPeriksa);

        $umurHari = (int) $interval->days;
        $umurBulan = ($interval->y * 12) + $interval->m;

        if ($umurHari > self::BATAS_USIA_HARI) {
            return [
                'tersedia' => false,
                'alasan' => 'Usia anak sudah mencapai atau melebihi 60 bulan.',
            ];
        }

        // Rentang berat badan yang digunakan WHO Anthro.
        if ($beratBadan < 0.9 || $beratBadan > 58) {
            return [
                'tersedia' => false,
                'alasan' => 'Berat badan berada di luar rentang perhitungan WHO.',
            ];
        }

        // WHO menggunakan panjang badan sebelum 731 hari
        // dan tinggi badan mulai 731 hari.
        $jenisUkuran = $umurHari < 731 ? 'P' : 'T';

        $minimum = $jenisUkuran === 'P' ? 45.0 : 65.0;
        $maksimum = $jenisUkuran === 'P' ? 110.0 : 120.0;

        if (
            $tinggiBadan < $minimum ||
            $tinggiBadan > $maksimum
        ) {
            return [
                'tersedia' => false,
                'alasan' => "Pengukuran berada di luar rentang {$minimum} sampai {$maksimum} cm.",
            ];
        }

        // Mengambil referensi LMS dengan interpolasi.
        $referensi = $this->getInterpolatedWflReference(
            $anak->jenis_kelamin,
            $jenisUkuran,
            $tinggiBadan
        );

        $zScore = $this->calculateLmsZScore(
            $beratBadan,
            $referensi
        );

        // Klasifikasi berdasarkan Permenkes No. 2 Tahun 2020.
        if ($zScore < -3) {
            $kategoriBerdasarkanZ = 'Gizi buruk';
        } elseif ($zScore < -2) {
            $kategoriBerdasarkanZ = 'Gizi kurang';
        } elseif ($zScore <= 1) {
            $kategoriBerdasarkanZ = 'Gizi baik';
        } elseif ($zScore <= 2) {
            $kategoriBerdasarkanZ = 'Berisiko gizi lebih';
        } elseif ($zScore <= 3) {
            $kategoriBerdasarkanZ = 'Gizi lebih';
        } else {
            $kategoriBerdasarkanZ = 'Obesitas';
        }

        // Flag WHO untuk BB/PB-TB adalah di luar -5 sampai +5 SD.
        $perluTinjauUlang = $zScore < -5 || $zScore > 5;
        $alasanTinjauUlang = null;

        if ($zScore < -5) {
            $alasanTinjauUlang = 'Z-score berada di bawah -5 SD.';
        } elseif ($zScore > 5) {
            $alasanTinjauUlang = 'Z-score berada di atas +5 SD.';
        }
        return [
            'tersedia' => true,
            'indikator' => 'BB_PB_TB',
            'nama_indikator' => 'Berat Badan Menurut Panjang/Tinggi Badan',

            'umur_hari' => $umurHari,
            'umur_bulan' => $umurBulan,

            'jenis_kelamin' => $anak->jenis_kelamin,
            'jenis_ukuran' => $jenisUkuran,

            'berat_badan' => $beratBadan,
            'tinggi_badan' => $tinggiBadan,

            'z_score' => round($zScore, 2),

            'kategori_berdasarkan_z_score' => $kategoriBerdasarkanZ,

            'nilai_ekstrem' => abs($zScore) > 3,

            'kategori' => $perluTinjauUlang
                ? 'Perlu pemeriksaan ulang'
                : $kategoriBerdasarkanZ,

            'perlu_tinjau_ulang' => $perluTinjauUlang,
            'alasan_tinjau_ulang' => $alasanTinjauUlang,
            'batas_flag' => [
                'minimum' => -5,
                'maksimum' => 5,
            ],
        ];
    }
    /**
     * Menggabungkan hasil analisis lima indikator antropometri.
     */
    public function analyzeAll(
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): array {

        if ($pemeriksaan->nik_anak !== $anak->nik_anak) {
            throw new InvalidArgumentException(
                'Data pemeriksaan tidak sesuai dengan anak.'
            );
        }

        $hasil = [
            'TB_U' => $this->analyzeHeightForAge(
                $anak,
                $pemeriksaan
            ),

            'BB_U' => $this->analyzeWeightForAge(
                $anak,
                $pemeriksaan
            ),

            'BB_PB_TB' => $this->analyzeWeightForLengthHeight(
                $anak,
                $pemeriksaan
            ),

            'IMT_U' => $this->analyzeBmiForAge(
                $anak,
                $pemeriksaan
            ),

            'LK_U' => $this->analyzeHeadCircumferenceForAge(
                $anak,
                $pemeriksaan
            ),
        ];

        $jumlahTersedia = count(array_filter(
            $hasil,
            fn($item) => ($item['tersedia'] ?? false) === true
        ));

        return [
            'tersedia' => $jumlahTersedia > 0,
            'jumlah_indikator' => count($hasil),
            'jumlah_indikator_tersedia' => $jumlahTersedia,

            'status_stunting' => $hasil['TB_U']['tersedia']
                ? $hasil['TB_U']['status_stunting']
                : null,

            'indikator' => $hasil,
        ];
    }
    /**
     * Interpolasi parameter LMS berdasarkan pengukuran
     * panjang atau tinggi badan dengan interval 0,1 cm.
     */
    private function getInterpolatedWflReference(
        string $jenisKelamin,
        string $jenisUkuran,
        float $nilaiUkur
    ): object {
        $minimum = $jenisUkuran === 'P' ? 45.0 : 65.0;
        $maksimum = $jenisUkuran === 'P' ? 110.0 : 120.0;

        if ($nilaiUkur < $minimum || $nilaiUkur > $maksimum) {
            throw new InvalidArgumentException(
                'Nilai pengukuran berada di luar rentang referensi WHO.'
            );
        }

        // Menentukan dua referensi terdekat.
        $nilaiBawah = floor(
            ($nilaiUkur + 0.000000001) * 10
        ) / 10;

        $nilaiAtas = min(
            round($nilaiBawah + 0.1, 1),
            $maksimum
        );

        $proporsi = ($nilaiUkur - $nilaiBawah) / 0.1;

        $referensiBawah = DB::table('referensi_antropometri_who')
            ->where('indikator', 'BB_PB_TB')
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('jenis_ukuran', $jenisUkuran)
            ->where(
                'nilai_ukur_cm',
                number_format($nilaiBawah, 1, '.', '')
            )
            ->first();

        if (!$referensiBawah) {
            throw new RuntimeException(
                'Referensi LMS pengukuran bawah tidak ditemukan.'
            );
        }

        // Jika nilai pengukuran tepat pada interval 0,1 cm,
        // tidak perlu melakukan interpolasi.
        if ($proporsi <= 0.00000001) {
            return $referensiBawah;
        }

        $referensiAtas = DB::table('referensi_antropometri_who')
            ->where('indikator', 'BB_PB_TB')
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('jenis_ukuran', $jenisUkuran)
            ->where(
                'nilai_ukur_cm',
                number_format($nilaiAtas, 1, '.', '')
            )
            ->first();

        if (!$referensiAtas) {
            throw new RuntimeException(
                'Referensi LMS pengukuran atas tidak ditemukan.'
            );
        }

        // Interpolasi L, M, dan S.
        return (object) [
            'l' => (float) $referensiBawah->l
                + $proporsi * (
                    (float) $referensiAtas->l
                    - (float) $referensiBawah->l
                ),

            'm' => (float) $referensiBawah->m
                + $proporsi * (
                    (float) $referensiAtas->m
                    - (float) $referensiBawah->m
                ),

            's' => (float) $referensiBawah->s
                + $proporsi * (
                    (float) $referensiAtas->s
                    - (float) $referensiBawah->s
                ),
        ];
    }

    /**
     * Menghitung Z-score LMS dengan koreksi SD23.
     */
    private function calculateLmsZScore(
        float $nilai,
        object $referensi
    ): float {
        $l = (float) $referensi->l;
        $m = (float) $referensi->m;
        $s = (float) $referensi->s;

        if ($nilai <= 0 || $m <= 0 || $s <= 0) {
            throw new InvalidArgumentException(
                'Nilai pengukuran atau parameter LMS tidak valid.'
            );
        }

        // Z-score LMS awal.
        if (abs($l) < 0.00000001) {
            $zInd = log($nilai / $m) / $s;
        } else {
            $zInd = (
                pow($nilai / $m, $l) - 1
            ) / ($l * $s);
        }

        if (!is_finite($zInd)) {
            throw new RuntimeException(
                'Hasil perhitungan Z-score tidak valid.'
            );
        }

        // SD23 correction: bagian atas.
        if ($zInd > 3) {
            $sd2Pos = $this->measurementAtZScore(
                $referensi,
                2
            );

            $sd3Pos = $this->measurementAtZScore(
                $referensi,
                3
            );

            $jarak = $sd3Pos - $sd2Pos;

            if ($jarak <= 0) {
                throw new RuntimeException(
                    'Jarak referensi SD2 dan SD3 tidak valid.'
                );
            }

            return 3 + (($nilai - $sd3Pos) / $jarak);
        }

        // SD23 correction: bagian bawah.
        if ($zInd < -3) {
            $sd2Neg = $this->measurementAtZScore(
                $referensi,
                -2
            );

            $sd3Neg = $this->measurementAtZScore(
                $referensi,
                -3
            );

            $jarak = $sd2Neg - $sd3Neg;

            if ($jarak <= 0) {
                throw new RuntimeException(
                    'Jarak referensi -2 SD dan -3 SD tidak valid.'
                );
            }

            return -3 + (($nilai - $sd3Neg) / $jarak);
        }

        return $zInd;
    }

    /**
     * Menghitung nilai pengukuran pada Z-score tertentu.
     *
     * Digunakan untuk memperoleh titik SD2 dan SD3.
     */
    private function measurementAtZScore(
        object $referensi,
        float $z
    ): float {
        $l = (float) $referensi->l;
        $m = (float) $referensi->m;
        $s = (float) $referensi->s;

        // Rumus jika L mendekati nol.
        if (abs($l) < 0.00000001) {
            return $m * exp($s * $z);
        }

        $basis = 1 + ($l * $s * $z);

        if ($basis <= 0) {
            throw new RuntimeException(
                'Parameter LMS menghasilkan nilai yang tidak valid.'
            );
        }

        return $m * pow($basis, 1 / $l);
    }
}
