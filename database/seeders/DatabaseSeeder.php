<?php

namespace Database\Seeders;

use App\Models\Bidan;
use App\Models\JenisVaksin;
use App\Models\Pengguna;
use App\Models\Posyandu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ───────────────────────────────────────────────
        Pengguna::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('admin123'),
                'role'     => 'SuperAdmin',
            ]
        );

        // ── Posyandu ──────────────────────────────────────────────────
        $posyandu1 = Posyandu::updateOrCreate(
            ['nama_posyandu' => 'Posyandu Mawar'],
            [
                'kecamatan'      => 'Lohbener',
                'desa_kelurahan' => 'Lohbener',
                'alamat'         => 'Jl. Mawar No. 10',
                'kabupaten_kota' => 'Indramayu',
                'password_kader' => Hash::make('kader123'),
            ]
        );

        $posyandu2 = Posyandu::updateOrCreate(
            ['nama_posyandu' => 'Posyandu Melati'],
            [
                'kecamatan'      => 'Sindang',
                'desa_kelurahan' => 'Sindang',
                'alamat'         => 'Jl. Melati No. 5',
                'kabupaten_kota' => 'Indramayu',
                'password_kader' => Hash::make('kader123'),
            ]
        );

        // ── Bidan Posyandu 1 ──────────────────────────────────────────
        $bidanUser1 = Pengguna::updateOrCreate(
            ['username' => 'bidan_sari'],
            [
                'password'          => Hash::make('password123'),
                'role'              => 'Bidan',
                'id_posyandu'       => $posyandu1->id_posyandu,
                'id_posyandu_aktif' => $posyandu1->id_posyandu,
            ]
        );

        // Bidan tidak punya kolom id_posyandu — hanya di pengguna
        Bidan::updateOrCreate(
            ['nip' => '199001012020012001'],
            [
                'id_user'    => $bidanUser1->id_user,
                'nama_bidan' => 'Sari Dewi, A.Md.Keb',
                'no_telp'    => '081234567890',
            ]
        );

        // ── Bidan Posyandu 2 ──────────────────────────────────────────
        $bidanUser2 = Pengguna::updateOrCreate(
            ['username' => 'bidan_rina'],
            [
                'password'          => Hash::make('password123'),
                'role'              => 'Bidan',
                'id_posyandu'       => $posyandu2->id_posyandu,
                'id_posyandu_aktif' => $posyandu2->id_posyandu,
            ]
        );

        Bidan::updateOrCreate(
            ['nip' => '199501012021012002'],
            [
                'id_user'    => $bidanUser2->id_user,
                'nama_bidan' => 'Rina Susanti, A.Md.Keb',
                'no_telp'    => '082345678901',
            ]
        );

        // ── Jenis Vaksin ──────────────────────────────────────────────
        $vaksinList = [
            ['nama_vaksin' => 'BCG',        'deskripsi' => 'Vaksin tuberkulosis'],
            ['nama_vaksin' => 'Hepatitis B', 'deskripsi' => 'Vaksin Hepatitis B'],
            ['nama_vaksin' => 'DPT-HB-Hib', 'deskripsi' => 'Vaksin kombinasi'],
            ['nama_vaksin' => 'Polio',       'deskripsi' => 'Vaksin polio oral'],
            ['nama_vaksin' => 'Campak',      'deskripsi' => 'Vaksin campak rubela'],
            ['nama_vaksin' => 'IPV',         'deskripsi' => 'Vaksin polio suntik'],
        ];

        foreach ($vaksinList as $v) {
            JenisVaksin::updateOrCreate(
                ['nama_vaksin' => $v['nama_vaksin']],
                ['deskripsi'   => $v['deskripsi']]
            );
        }
    }
}