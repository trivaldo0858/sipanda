<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::createIfNotExists('posyandu', function (Blueprint $table) {
            $table->id('id_posyandu');
            $table->string('nama_posyandu');
            $table->string('kecamatan');
            $table->string('desa_kelurahan');
            $table->text('alamat');
            $table->string('kabupaten_kota')->default('Indramayu');
            $table->string('password_kader'); // Password shared untuk semua Kader posyandu ini
            $table->timestamps();
        });

        // Tambah foreign key setelah posyandu dibuat
        Schema::table('pengguna', function (Blueprint $table) {
            $table->foreign('id_posyandu')
                  ->references('id_posyandu')
                  ->on('posyandu')
                  ->nullOnDelete();

            $table->foreign('id_posyandu_aktif')
                  ->references('id_posyandu')
                  ->on('posyandu')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu']);
            $table->dropForeign(['id_posyandu_aktif']);
        });
        Schema::dropIfExists('posyandu');
    }
};