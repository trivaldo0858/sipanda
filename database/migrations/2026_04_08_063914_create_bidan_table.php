<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bidan', function (Blueprint $table) {
            $table->string('nip')->primary();
            $table->unsignedBigInteger('id_user');
            $table->string('nama_bidan');
            $table->string('no_telp')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bidan');
    }
};