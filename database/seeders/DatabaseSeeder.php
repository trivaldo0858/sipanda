<?php

namespace Database\Seeders;

use App\Models\Bidan;
use App\Models\JenisVaksin;
use App\Models\Kader;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ──────────────────────────────────────────────
        Pengguna::create([
            'username' => 'superadmin',
            'password' => Hash::make('admin123'),
            'role'     => 'SuperAdmin',
        ]);

        // ── Data Posyandu ────────────────────────────────────────────
        $posyandu1 = Posyandu::create([
            'nama_posyandu' => 'Posyandu Mawar',
            'alamat'        => 'Jl. Mawar No. 10, RW 03',
            'wilayah'       => 'RW 03 Desa Sukamaju',
            'no_telp'       => '081234567890',
            'status'        => 'Aktif',
        ]);

        $posyandu2 = Posyandu::create([
            'nama_posyandu' => 'Posyandu Melati',
            'alamat'        => 'Jl. Melati No. 5, RW 07',
            'wilayah'       => 'RW 07 Desa Sukamaju',
            'no_telp'       => '082345678901',
            'status'        => 'Aktif',
        ]);

        // ── Akun Bidan — Posyandu 1 ──────────────────────────────────
        $bidanUser1 = Pengguna::create([
            'username'    => 'bidan_sari',
            'password'    => Hash::make('password123'),
            'role'        => 'Bidan',
            'id_posyandu' => $posyandu1->id_posyandu,
        ]);
        Bidan::create([
            'nip'         => '199001012020012001',
            'id_user'     => $bidanUser1->id_user,
            'nama_bidan'  => 'Sari Dewi, A.Md.Keb',
            'no_telp'     => '081234567891',
            'id_posyandu' => $posyandu1->id_posyandu,
        ]);

        // ── Akun Bidan — Posyandu 2 ──────────────────────────────────
        $bidanUser2 = Pengguna::create([
            'username'    => 'bidan_rina',
            'password'    => Hash::make('password123'),
            'role'        => 'Bidan',
            'id_posyandu' => $posyandu2->id_posyandu,
        ]);
        Bidan::create([
            'nip'         => '199501012021012002',
            'id_user'     => $bidanUser2->id_user,
            'nama_bidan'  => 'Rina Susanti, A.Md.Keb',
            'no_telp'     => '082345678902',
            'id_posyandu' => $posyandu2->id_posyandu,
        ]);

        // ── Akun Kader — Posyandu 1 ──────────────────────────────────
        $kaderUser1 = Pengguna::create([
            'username'    => 'kader_ani',
            'password'    => Hash::make('password123'),
            'role'        => 'Kader',
            'id_posyandu' => $posyandu1->id_posyandu,
        ]);
        Kader::create([
            'id_user'     => $kaderUser1->id_user,
            'nama_kader'  => 'Ani Wulandari',
            'wilayah'     => 'RW 03 Desa Sukamaju',
            'id_posyandu' => $posyandu1->id_posyandu,
        ]);

        // ── Akun Kader — Posyandu 2 ──────────────────────────────────
        $kaderUser2 = Pengguna::create([
            'username'    => 'kader_tuti',
            'password'    => Hash::make('password123'),
            'role'        => 'Kader',
            'id_posyandu' => $posyandu2->id_posyandu,
        ]);
        Kader::create([
            'id_user'     => $kaderUser2->id_user,
            'nama_kader'  => 'Tuti Handayani',
            'wilayah'     => 'RW 07 Desa Sukamaju',
            'id_posyandu' => $posyandu2->id_posyandu,
        ]);

        // ── Jenis Vaksin ─────────────────────────────────────────────
        $vaksinList = [
            ['nama_vaksin' => 'BCG',        'deskripsi' => 'Vaksin tuberkulosis'],
            ['nama_vaksin' => 'Hepatitis B', 'deskripsi' => 'Vaksin Hepatitis B'],
            ['nama_vaksin' => 'DPT-HB-Hib', 'deskripsi' => 'Vaksin kombinasi'],
            ['nama_vaksin' => 'Polio',       'deskripsi' => 'Vaksin polio oral'],
            ['nama_vaksin' => 'Campak',      'deskripsi' => 'Vaksin campak rubela'],
            ['nama_vaksin' => 'IPV',         'deskripsi' => 'Vaksin polio suntik'],
        ];

        foreach ($vaksinList as $v) {
            JenisVaksin::create($v);
        }
    }
}