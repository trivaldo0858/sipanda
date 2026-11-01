<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referensi_antropometri_who', function (Blueprint $table) {
            $table->id();

            // Kode unik setiap baris referensi WHO.
            $table->string('kode_referensi', 80)->unique();

            // TB_U, BB_U, BB_PB_TB, LK_U.
            $table->string('indikator', 20);

            // L = Laki-laki, P = Perempuan.
            $table->char('jenis_kelamin', 1);

            // Digunakan untuk indikator berdasarkan umur.
            $table->unsignedSmallInteger('umur_hari')->nullable();

            // Digunakan untuk indikator berdasarkan panjang/tinggi.
            $table->decimal('nilai_ukur_cm', 5, 1)->nullable();

            // P = Panjang badan, T = Tinggi badan.
            $table->char('jenis_ukuran', 1)->nullable();

            // Parameter LMS dari standar WHO.
            $table->decimal('l', 12, 8);
            $table->decimal('m', 12, 8);
            $table->decimal('s', 12, 8);

            // Nama file sumber WHO.
            $table->string('sumber_file', 40);

            $table->index(
                ['indikator', 'jenis_kelamin', 'umur_hari'],
                'who_referensi_umur_index'
            );

            $table->index(
                ['indikator', 'jenis_kelamin', 'jenis_ukuran', 'nilai_ukur_cm'],
                'who_referensi_ukuran_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referensi_antropometri_who');
    }
};
