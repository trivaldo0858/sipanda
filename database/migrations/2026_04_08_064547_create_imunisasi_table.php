<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imunisasi', function (Blueprint $table) {
            $table->id('id_imunisasi');
            $table->string('nik_anak');
            $table->string('nip_bidan')->nullable();
            $table->unsignedBigInteger('id_vaksin');
            $table->date('tgl_pemberian');
            $table->timestamps();

            $table->foreign('nik_anak')->references('nik_anak')->on('anak')->onDelete('cascade');
            $table->foreign('nip_bidan')->references('nip')->on('bidan')->nullOnDelete();
            $table->foreign('id_vaksin')->references('id_vaksin')->on('jenis_vaksin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imunisasi');
    }
};