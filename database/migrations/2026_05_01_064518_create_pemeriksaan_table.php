<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemeriksaan', function (Blueprint $table) {
            $table->id('id_periksa'); // Primary Key

            // Hubungan ke tabel anak (Pastikan tabel 'anak' sudah bermigrasi sebelumnya)
            $table->string('nik_anak');
            $table->foreign('nik_anak')->references('nik_anak')->on('anak')->onDelete('cascade');

            // Hubungan ke tabel posyandu (Penyebab Error Sebelumnya)
            // Pastikan menggunakan foreignId agar tipe datanya otomatis Unsigned Big Integer
            $table->foreignId('id_posyandu')->constrained('posyandu', 'id_posyandu')->onDelete('cascade');

            $table->string('nip_bidan')->nullable();
            $table->foreign('nip_bidan')->references('nip')->on('bidan')->nullOnDelete();

            $table->date('tgl_periksa');
            $table->float('berat_badan')->nullable();
            $table->float('tinggi_badan')->nullable();
            $table->float('lingkar_kepala')->nullable();
            $table->text('keluhan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan');
    }
};