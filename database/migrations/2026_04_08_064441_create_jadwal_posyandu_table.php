<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_posyandu', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->unsignedBigInteger('id_kader');
            $table->date('tgl_kegiatan');
            $table->string('lokasi');
            $table->text('agenda')->nullable();
            $table->timestamps();

            $table->foreign('id_kader')->references('id_kader')->on('kader')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_posyandu');
    }
};