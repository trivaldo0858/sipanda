<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin', // Gunakan ini untuk login
            'email' => 'admin@sipanda.id',
            'password' => Hash::make('admin123'), // Password di-hash agar aman
            'role' => 'SuperAdmin',
            'id_posyandu' => null, // Super Admin tidak terikat satu posyandu
        ]);
    }
}