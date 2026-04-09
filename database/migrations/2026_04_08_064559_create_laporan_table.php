<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->string('nip_bidan');
            $table->enum('jenis_laporan', ['Bulanan', 'Tahunan']);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->date('tgl_cetak');
            $table->timestamps();

            $table->foreign('nip_bidan')->references('nip')->on('bidan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};