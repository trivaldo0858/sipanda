<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class JadwalImunisasiSeeder extends Seeder
{
    public function run(): void
    {
        $vaksin = DB::table('jenis_vaksin')
            ->pluck('id_vaksin', 'nama_vaksin');

        $wajib = [
            'BCG',
            'Hepatitis B',
            'DPT-HB-Hib',
            'Polio',
            'Campak',
            'IPV',
            'PCV',
            'Rotavirus',
            'JE',
        ];

        foreach ($wajib as $nama) {
            if (!isset($vaksin[$nama])) {
                throw new RuntimeException(
                    "Jenis vaksin {$nama} tidak ditemukan."
                );
            }
        }

        // [nama vaksin, dosis, usia hari, usia bulan, endemis, catatan]
        $jadwal = [
            [
                'Hepatitis B',
                1,
                0,
                null,
                false,
                'HB0: sasaran dalam 24 jam setelah lahir.'
            ],

            ['BCG', 1, null, 1, false, null],

            ['Polio', 1, null, 1, false, 'OPV dosis 1'],
            ['Polio', 2, null, 2, false, 'OPV dosis 2'],
            ['Polio', 3, null, 3, false, 'OPV dosis 3'],
            ['Polio', 4, null, 4, false, 'OPV dosis 4'],

            ['DPT-HB-Hib', 1, null, 2, false, null],
            ['DPT-HB-Hib', 2, null, 3, false, null],
            ['DPT-HB-Hib', 3, null, 4, false, null],
            [
                'DPT-HB-Hib',
                4,
                null,
                18,
                false,
                'Imunisasi lanjutan'
            ],

            ['PCV', 1, null, 2, false, null],
            ['PCV', 2, null, 3, false, null],
            ['PCV', 3, null, 12, false, null],

            ['Rotavirus', 1, null, 2, false, null],
            ['Rotavirus', 2, null, 3, false, null],
            [
                'Rotavirus',
                3,
                null,
                4,
                false,
                'Perhatikan batas usia pemberian vaksin.'
            ],

            ['IPV', 1, null, 4, false, null],
            ['IPV', 2, null, 9, false, null],

            [
                'Campak',
                1,
                null,
                9,
                false,
                'Campak-Rubela dosis 1'
            ],
            [
                'Campak',
                2,
                null,
                18,
                false,
                'Campak-Rubela dosis 2'
            ],

            [
                'JE',
                1,
                null,
                10,
                true,
                'Hanya untuk wilayah endemis JE.'
            ],
        ];

        foreach ($jadwal as $item) {
            [
                $nama,
                $dosis,
                $usiaHari,
                $usiaBulan,
                $endemis,
                $catatan,
            ] = $item;

            DB::table('jadwal_imunisasi')->updateOrInsert(
                [
                    'id_vaksin' => $vaksin[$nama],
                    'urutan_dosis' => $dosis,
                ],
                [
                    'usia_hari' => $usiaHari,
                    'usia_bulan' => $usiaBulan,
                    'khusus_wilayah_endemis' => $endemis,
                    'catatan' => $catatan,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
