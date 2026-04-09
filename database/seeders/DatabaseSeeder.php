<?php

namespace Database\Seeders;

use App\Models\Bidan;
use App\Models\JenisVaksin;
use App\Models\Kader;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Akun Bidan ──────────────────────────────────────────────
        $bidan = Pengguna::create([
            'username' => 'bidan_sari',
            'password' => Hash::make('password123'),
            'role'     => 'Bidan',
        ]);
        Bidan::create([
            'nip'        => '199001012020012001',
            'id_user'    => $bidan->id_user,
            'nama_bidan' => 'Sari Dewi, A.Md.Keb',
            'no_telp'    => '081234567890',
        ]);

        // ── Akun Kader ──────────────────────────────────────────────
        $kader = Pengguna::create([
            'username' => 'kader_rina',
            'password' => Hash::make('password123'),
            'role'     => 'Kader',
        ]);
        Kader::create([
            'id_user'    => $kader->id_user,
            'nama_kader' => 'Rina Wulandari',
            'wilayah'    => 'RW 03 Desa Sukamaju',
        ]);

        // ── Jenis Vaksin ────────────────────────────────────────────
        $vaksinList = [
            ['nama_vaksin' => 'BCG',         'deskripsi' => 'Vaksin untuk mencegah tuberkulosis'],
            ['nama_vaksin' => 'Hepatitis B',  'deskripsi' => 'Vaksin Hepatitis B dosis pertama'],
            ['nama_vaksin' => 'DPT-HB-Hib',  'deskripsi' => 'Vaksin kombinasi difteri, pertussis, tetanus, hepatitis B, dan Hib'],
            ['nama_vaksin' => 'Polio',        'deskripsi' => 'Vaksin polio oral (OPV)'],
            ['nama_vaksin' => 'Campak',       'deskripsi' => 'Vaksin campak rubela (MR)'],
            ['nama_vaksin' => 'IPV',          'deskripsi' => 'Vaksin polio suntik (inactivated)'],
        ];

        foreach ($vaksinList as $v) {
            JenisVaksin::create($v);
        }
    }
}