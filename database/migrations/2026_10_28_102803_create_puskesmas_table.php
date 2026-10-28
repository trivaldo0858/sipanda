<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('puskesmas', function (Blueprint $table) {
            $table->id('id_puskesmas');
            $table->string('nama_puskesmas');
            $table->text('alamat');
            $table->string('kecamatan')->nullable();
            $table->string('desa_kelurahan')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('hari_operasional')->nullable();
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puskesmas');
    }
};
