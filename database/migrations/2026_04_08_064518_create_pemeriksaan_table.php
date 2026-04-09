<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan', function (Blueprint $table) {
            $table->id('id_pemeriksaan');
            $table->string('nik_anak');
            $table->unsignedBigInteger('id_kader');
            $table->string('nip_bidan')->nullable();
            $table->unsignedBigInteger('id_jadwal')->nullable();
            $table->date('tgl_pemeriksaan');
            $table->float('berat_badan')->nullable();
            $table->float('tinggi_badan')->nullable();
            $table->float('lingkar_kepala')->nullable();
            $table->text('keluhan')->nullable();
            $table->timestamps();

            $table->foreign('nik_anak')->references('nik_anak')->on('anak')->onDelete('cascade');
            $table->foreign('id_kader')->references('id_kader')->on('kader');
            $table->foreign('nip_bidan')->references('nip')->on('bidan')->nullOnDelete();
            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_posyandu')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan');
    }
};