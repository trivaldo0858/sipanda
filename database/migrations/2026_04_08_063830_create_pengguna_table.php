<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('username')->unique()->nullable(); // Kader tidak pakai username
            $table->string('password')->nullable();           // Kader tidak pakai password personal
            $table->enum('role', ['SuperAdmin', 'Bidan', 'Kader', 'OrangTua']);
            $table->unsignedBigInteger('id_posyandu')->nullable(); // Posyandu utama
            $table->unsignedBigInteger('id_posyandu_aktif')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};