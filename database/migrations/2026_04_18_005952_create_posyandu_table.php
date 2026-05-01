<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posyandu', function (Blueprint $table) {
            $table->id('id_posyandu');
            $table->string('nama_posyandu');
            $table->string('kecamatan');
            $table->string('desa_kelurahan');
            $table->text('alamat');
            $table->string('kabupaten_kota')->default('Indramayu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu');
    }
};