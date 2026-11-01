<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReferensiAntropometriWhoSeeder extends Seeder
{
    public function run(): void
    {
        $folder = database_path('data/who');

        $files = [
            'lenanthro.txt' => [
                'indikator' => 'TB_U',
                'tipe' => 'umur',
                'kolom_ukuran' => 'age',
                'kolom_jenis' => 'loh',
            ],
            'weianthro.txt' => [
                'indikator' => 'BB_U',
                'tipe' => 'umur',
                'kolom_ukuran' => 'age',
            ],
            'bmianthro.txt' => [
                'indikator' => 'IMT_U',
                'tipe' => 'umur',
                'kolom_ukuran' => 'age',
                'kolom_jenis' => 'loh',
            ],
            'wflanthro.txt' => [
                'indikator' => 'BB_PB_TB',
                'tipe' => 'ukuran',
                'kolom_ukuran' => 'length',
                'kolom_jenis' => 'lorh',
                'jenis_ukuran' => 'P',
            ],
            'wfhanthro.txt' => [
                'indikator' => 'BB_PB_TB',
                'tipe' => 'ukuran',
                'kolom_ukuran' => 'height',
                'kolom_jenis' => 'lorh',
                'jenis_ukuran' => 'T',
            ],
            'hcanthro.txt' => [
                'indikator' => 'LK_U',
                'tipe' => 'umur',
                'kolom_ukuran' => 'age',
            ],
        ];

        $totalImported = 0;

        foreach ($files as $filename => $config) {
            $path = $folder . DIRECTORY_SEPARATOR . $filename;

            if (!is_file($path)) {
                throw new RuntimeException(
                    "File referensi WHO tidak ditemukan: {$filename}"
                );
            }

            $handle = fopen($path, 'r');

            if ($handle === false) {
                throw new RuntimeException(
                    "File WHO tidak dapat dibaca: {$filename}"
                );
            }

            $header = fgetcsv($handle, null, "\t");

            if (!$header) {
                fclose($handle);

                throw new RuntimeException(
                    "Header file WHO kosong: {$filename}"
                );
            }

            $header = array_map(
                fn($column) => strtolower(trim($column)),
                $header
            );

            $required = [
                'sex',
                $config['kolom_ukuran'],
                'l',
                'm',
                's',
            ];

            if (isset($config['kolom_jenis'])) {
                $required[] = $config['kolom_jenis'];
            }

            foreach ($required as $column) {
                if (!in_array($column, $header, true)) {
                    fclose($handle);

                    throw new RuntimeException(
                        "Kolom {$column} tidak ditemukan pada {$filename}"
                    );
                }
            }

            $index = array_flip($header);
            $batch = [];

            while (($columns = fgetcsv($handle, null, "\t")) !== false) {
                if ($columns === [null]) {
                    continue;
                }

                $columns = array_map(
                    fn($value) => trim((string) $value),
                    $columns
                );

                if (count($columns) !== count($header)) {
                    fclose($handle);

                    throw new RuntimeException(
                        "Jumlah kolom tidak sesuai pada {$filename}"
                    );
                }

                $row = [];

                foreach ($index as $name => $position) {
                    $row[$name] = $columns[$position];
                }

                $sex = (int) $row['sex'];

                if (!in_array($sex, [1, 2], true)) {
                    continue;
                }

                $jenisKelamin = $sex === 1 ? 'L' : 'P';

                $l = $row['l'];
                $m = $row['m'];
                $s = $row['s'];
                $ukuran = $row[$config['kolom_ukuran']];

                if (
                    !is_numeric($l) ||
                    !is_numeric($m) ||
                    !is_numeric($s) ||
                    !is_numeric($ukuran)
                ) {
                    fclose($handle);

                    throw new RuntimeException(
                        "Parameter LMS tidak valid pada {$filename}"
                    );
                }

                $jenisUkuran = null;
                $umurHari = null;
                $nilaiUkur = null;

                if ($config['tipe'] === 'umur') {
                    $umurHari = (int) $ukuran;

                    if ($umurHari < 0) {
                        fclose($handle);

                        throw new RuntimeException(
                            "Umur referensi tidak valid pada {$filename}"
                        );
                    }

                    if (isset($config['kolom_jenis'])) {
                        $marker = strtoupper(
                            $row[$config['kolom_jenis']]
                        );

                        $jenisUkuran = match ($marker) {
                            'L' => 'P',
                            'H' => 'T',
                            default => throw new RuntimeException(
                                "Penanda panjang/tinggi tidak valid pada {$filename}"
                            ),
                        };
                    }

                    $kode = implode('-', [
                        $config['indikator'],
                        $jenisKelamin,
                        $umurHari,
                    ]);
                } else {
                    $nilaiUkur = round((float) $ukuran, 1);

                    $marker = strtoupper(
                        $row[$config['kolom_jenis']]
                    );

                    $jenisUkuran = match ($marker) {
                        'L' => 'P',
                        'H' => 'T',
                        default => $config['jenis_ukuran'],
                    };

                    $kode = implode('-', [
                        $config['indikator'],
                        $jenisKelamin,
                        $jenisUkuran,
                        number_format($nilaiUkur, 1, '.', ''),
                    ]);
                }

                $batch[] = [
                    'kode_referensi' => $kode,
                    'indikator' => $config['indikator'],
                    'jenis_kelamin' => $jenisKelamin,
                    'umur_hari' => $umurHari,
                    'nilai_ukur_cm' => $nilaiUkur,
                    'jenis_ukuran' => $jenisUkuran,
                    'l' => $l,
                    'm' => $m,
                    's' => $s,
                    'sumber_file' => $filename,
                ];

                if (count($batch) >= 1000) {
                    $this->saveBatch($batch);
                    $totalImported += count($batch);
                    $batch = [];
                }
            }

            fclose($handle);

            if (!empty($batch)) {
                $this->saveBatch($batch);
                $totalImported += count($batch);
            }

            $this->command->info(
                "{$filename}: selesai diproses."
            );
        }

        $this->command->info(
            "Total baris referensi WHO diproses: {$totalImported}"
        );
    }

    private function saveBatch(array $batch): void
    {
        DB::table('referensi_antropometri_who')->upsert(
            $batch,
            ['kode_referensi'],
            [
                'indikator',
                'jenis_kelamin',
                'umur_hari',
                'nilai_ukur_cm',
                'jenis_ukuran',
                'l',
                'm',
                's',
                'sumber_file',
            ]
        );
    }
}
