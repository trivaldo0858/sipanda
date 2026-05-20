<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anak', function (Blueprint $table) {
            $table->string('nik_anak')->primary();
            $table->string('nik_orang_tua');   // FK ke orang_tua
            $table->unsignedBigInteger('id_posyandu');
            $table->string('nama_anak');
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_ayah')->nullable();
            $table->timestamps();

            $table->foreign('nik_orang_tua')
                  ->references('nik_orang_tua')
                  ->on('orang_tua')
                  ->onDelete('cascade');

            $table->foreign('id_posyandu')
                  ->references('id_posyandu')
                  ->on('posyandu')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak');
    }
};