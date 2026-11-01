<?php

namespace App\Console\Commands;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use App\Services\AnthropometricService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class ExportStuntingDataset extends Command
{
    protected $signature = 'ml:export-stunting-dataset';

    protected $description = 'Mengekspor dataset historis untuk prediksi risiko stunting';

    public function handle(AnthropometricService $service): int
    {
        $this->info('Memulai ekspor dataset prediksi stunting...');

        $appKey = (string) config('app.key');

        if ($appKey === '') {
            $this->error('APP_KEY belum tersedia.');

            return self::FAILURE;
        }

        // Ambil data anak.
        $anakList = Anak::query()
            ->get([
                'nik_anak',
                'tgl_lahir',
                'jenis_kelamin',
            ]);

        // Ambil seluruh pemeriksaan dan kelompokkan berdasarkan anak.
        $pemeriksaanPerAnak = Pemeriksaan::query()
            ->orderBy('tgl_periksa')
            ->orderBy('id_periksa')
            ->get()
            ->groupBy('nik_anak');

        // Siapkan lokasi file CSV.
        $direktori = base_path('ml/data/raw');

        File::ensureDirectoryExists($direktori);

        $lokasiFile = $direktori . '/dataset_prediksi_stunting.csv';

        $handle = fopen($lokasiFile, 'wb');

        if ($handle === false) {
            $this->error('File dataset tidak dapat dibuat.');

            return self::FAILURE;
        }

        $header = [
            'group_id',
            'umur_hari_awal',
            'jenis_kelamin',
            'berat_badan_awal_kg',
            'tinggi_badan_awal_cm',
            'haz_awal',
            'waz_awal',
            'whz_awal',
            'baz_awal',
            'jarak_pemeriksaan_sebelumnya_hari',
            'perubahan_berat_badan_kg',
            'perubahan_tinggi_badan_cm',
            'perubahan_haz',
            'jarak_hari_target',
            'haz_target',
            'label_risiko',
        ];

        fputcsv($handle, $header, ',', '"', '\\');

        $jumlahPasangan = 0;
        $jumlahDiekspor = 0;
        $jumlahDilewati = 0;

        foreach ($anakList as $anak) {
            if (
                !$anak->tgl_lahir ||
                !in_array($anak->jenis_kelamin, ['L', 'P'], true)
            ) {
                continue;
            }

            $daftarPemeriksaan = $pemeriksaanPerAnak
                ->get($anak->nik_anak, collect())
                ->values();

            if ($daftarPemeriksaan->count() < 2) {
                continue;
            }

            $tanggalLahir = CarbonImmutable::parse(
                $anak->tgl_lahir
            )->startOfDay();

            foreach ($daftarPemeriksaan as $index => $awal) {
                if (!$awal->tgl_periksa) {
                    continue;
                }

                $tanggalAwal = CarbonImmutable::parse(
                    $awal->tgl_periksa
                )->startOfDay();

                if ($tanggalAwal->lessThan($tanggalLahir)) {
                    continue;
                }

                // Cari pemeriksaan berikutnya dengan jarak 75–105 hari.
                // Pilih yang paling dekat dengan hari ke-90.
                $kandidatTarget = $daftarPemeriksaan
                    ->slice($index + 1)
                    ->filter(function ($item) use ($tanggalAwal, $tanggalLahir) {
                        if (!$item->tgl_periksa) {
                            return false;
                        }

                        $tanggalTarget = CarbonImmutable::parse(
                            $item->tgl_periksa
                        )->startOfDay();

                        $jarak = $tanggalAwal->diffInDays(
                            $tanggalTarget
                        );

                        return $tanggalTarget->greaterThan($tanggalAwal)
                            && $tanggalTarget->greaterThanOrEqualTo($tanggalLahir)
                            && $jarak >= 75
                            && $jarak <= 105;
                    })
                    ->sortBy(function ($item) use ($tanggalAwal) {
                        $tanggalTarget = CarbonImmutable::parse(
                            $item->tgl_periksa
                        )->startOfDay();

                        return abs(
                            $tanggalAwal->diffInDays($tanggalTarget) - 90
                        );
                    })
                    ->values();

                $target = $kandidatTarget->first();

                if (!$target) {
                    continue;
                }

                $jumlahPasangan++;

                try {
                    // Pemeriksaan awal wajib memiliki BB dan TB.
                    if (
                        !$this->nilaiPositif($awal->berat_badan) ||
                        !$this->nilaiPositif($awal->tinggi_badan)
                    ) {
                        $jumlahDilewati++;
                        continue;
                    }

                    // Pemeriksaan target wajib memiliki TB.
                    if (!$this->nilaiPositif($target->tinggi_badan)) {
                        $jumlahDilewati++;
                        continue;
                    }

                    $tanggalTarget = CarbonImmutable::parse(
                        $target->tgl_periksa
                    )->startOfDay();

                    $umurAwal = (int) $tanggalLahir->diffInDays(
                        $tanggalAwal
                    );

                    $umurTarget = (int) $tanggalLahir->diffInDays(
                        $tanggalTarget
                    );

                    if (
                        $umurAwal > 1826 ||
                        $umurTarget > 1826
                    ) {
                        $jumlahDilewati++;
                        continue;
                    }

                    // Hitung HAZ pemeriksaan awal.
                    $hasilHazAwal = $service->analyzeHeightForAge(
                        $anak,
                        $awal
                    );

                    // Hitung HAZ pemeriksaan target.
                    $hasilHazTarget = $service->analyzeHeightForAge(
                        $anak,
                        $target
                    );

                    // HAZ harus tersedia dan tidak melewati flag WHO.
                    if (
                        !($hasilHazAwal['tersedia'] ?? false) ||
                        !($hasilHazTarget['tersedia'] ?? false) ||
                        ($hasilHazAwal['perlu_tinjau_ulang'] ?? true) ||
                        ($hasilHazTarget['perlu_tinjau_ulang'] ?? true)
                    ) {
                        $jumlahDilewati++;
                        continue;
                    }

                    $hazAwal = $hasilHazAwal['z_score'];
                    $hazTarget = $hasilHazTarget['z_score'];

                    // Ambil indikator tambahan pada pemeriksaan awal.
                    $wazAwal = $this->ambilZScore(
                        fn() => $service->analyzeWeightForAge(
                            $anak,
                            $awal
                        )
                    );

                    $whzAwal = $this->ambilZScore(
                        fn() => $service->analyzeWeightForLengthHeight(
                            $anak,
                            $awal
                        )
                    );

                    $bazAwal = $this->ambilZScore(
                        fn() => $service->analyzeBmiForAge(
                            $anak,
                            $awal
                        )
                    );

                    // Hitung perubahan dari pemeriksaan sebelumnya.
                    $sebelumnya = $index > 0
                        ? $daftarPemeriksaan[$index - 1]
                        : null;

                    $jarakSebelumnya = null;
                    $perubahanBB = null;
                    $perubahanTB = null;
                    $perubahanHAZ = null;

                    if ($sebelumnya && $sebelumnya->tgl_periksa) {
                        $tanggalSebelumnya = CarbonImmutable::parse(
                            $sebelumnya->tgl_periksa
                        )->startOfDay();

                        if ($tanggalSebelumnya->lessThan($tanggalAwal)) {
                            $jarakSebelumnya = (int) $tanggalSebelumnya
                                ->diffInDays($tanggalAwal);

                            if (
                                $this->nilaiPositif($sebelumnya->berat_badan)
                            ) {
                                $perubahanBB = (float) $awal->berat_badan
                                    - (float) $sebelumnya->berat_badan;
                            }

                            if (
                                $this->nilaiPositif($sebelumnya->tinggi_badan)
                            ) {
                                $perubahanTB = (float) $awal->tinggi_badan
                                    - (float) $sebelumnya->tinggi_badan;
                            }

                            if (
                                $this->nilaiPositif($sebelumnya->tinggi_badan)
                            ) {
                                $hasilHazSebelumnya = $this->analisisHazAman(
                                    $service,
                                    $anak,
                                    $sebelumnya
                                );

                                if ($hasilHazSebelumnya !== null) {
                                    $perubahanHAZ = $hazAwal
                                        - $hasilHazSebelumnya;
                                }
                            }
                        }
                    }

                    // Bentuk label dari kategori TB/U pada pemeriksaan target.
                    $labelRisiko = match (
                    $hasilHazTarget['kategori_berdasarkan_z_score']
                    ) {
                        'Sangat pendek' => 'tinggi',
                        'Pendek' => 'sedang',
                        default => 'rendah',
                    };

                    // NIK tidak diekspor.
                    // Gunakan HMAC agar anak yang sama tetap memiliki group_id.
                    $groupId = hash_hmac(
                        'sha256',
                        (string) $anak->nik_anak,
                        $appKey
                    );

                    $baris = [
                        $groupId,
                        $umurAwal,
                        $anak->jenis_kelamin,
                        (float) $awal->berat_badan,
                        (float) $awal->tinggi_badan,
                        $hazAwal,
                        $wazAwal,
                        $whzAwal,
                        $bazAwal,
                        $jarakSebelumnya,
                        $perubahanBB,
                        $perubahanTB,
                        $perubahanHAZ,
                        $tanggalAwal->diffInDays($tanggalTarget),
                        $hazTarget,
                        $labelRisiko,
                    ];

                    fputcsv($handle, $baris, ',', '"', '\\');

                    $jumlahDiekspor++;
                } catch (Throwable $exception) {
                    report($exception);

                    $jumlahDilewati++;
                }
            }
        }

        fclose($handle);

        $this->newLine();

        $this->info('Ekspor dataset selesai.');

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Pasangan pemeriksaan ditemukan', $jumlahPasangan],
                ['Baris berhasil diekspor', $jumlahDiekspor],
                ['Pasangan dilewati', $jumlahDilewati],
            ]
        );

        $this->info('Lokasi file: ' . $lokasiFile);

        if ($jumlahDiekspor === 0) {
            $this->warn(
                'Belum ada sampel yang memenuhi syarat untuk dataset.'
            );
        }

        return self::SUCCESS;
    }

    private function nilaiPositif(mixed $nilai): bool
    {
        return is_numeric($nilai)
            && is_finite((float) $nilai)
            && (float) $nilai > 0;
    }

    private function ambilZScore(callable $analisis): ?float
    {
        try {
            $hasil = $analisis();

            if (
                !($hasil['tersedia'] ?? false) ||
                ($hasil['perlu_tinjau_ulang'] ?? false)
            ) {
                return null;
            }

            return isset($hasil['z_score'])
                ? (float) $hasil['z_score']
                : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function analisisHazAman(
        AnthropometricService $service,
        Anak $anak,
        Pemeriksaan $pemeriksaan
    ): ?float {
        $hasil = $this->ambilZScore(
            fn() => $service->analyzeHeightForAge(
                $anak,
                $pemeriksaan
            )
        );

        return $hasil;
    }
}
