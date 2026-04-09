<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kader', function (Blueprint $table) {
            $table->id('id_kader');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_kader');
            $table->string('wilayah')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kader');
    }
};