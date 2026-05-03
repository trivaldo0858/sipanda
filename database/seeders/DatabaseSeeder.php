<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\JenisVaksin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::create([
            'username' => 'superadmin',
            'password' => Hash::make('admin123'),
            'role' => 'SuperAdmin',
        ]);

        $vaksinList = [
            ['nama_vaksin' => 'BCG', 'deskripsi' => 'Vaksin tuberkulosis'],
            ['nama_vaksin' => 'Hepatitis B', 'deskripsi' => 'Vaksin Hepatitis B'],
            ['nama_vaksin' => 'DPT-HB-Hib', 'deskripsi' => 'Vaksin kombinasi'],
            ['nama_vaksin' => 'Polio', 'deskripsi' => 'Vaksin polio oral'],
            ['nama_vaksin' => 'Campak', 'deskripsi' => 'Vaksin campak rubela'],
        ];

        foreach ($vaksinList as $v) {
            JenisVaksin::create($v);
        }

    }
}