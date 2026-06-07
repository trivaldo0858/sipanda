<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posyandu')) {
            Schema::create('posyandu', function (Blueprint $table) {
                $table->id('id_posyandu');
                $table->string('nama_posyandu');
                $table->string('kecamatan');
                $table->string('desa_kelurahan');
                $table->text('alamat');
                $table->string('kabupaten_kota')->default('Indramayu');
                $table->string('password_kader');
                $table->timestamps();
            });
        }

        // Tambah foreign key jika belum ada
        if (Schema::hasTable('pengguna') && Schema::hasTable('posyandu')) {
            Schema::table('pengguna', function (Blueprint $table) {
                // Cek dulu agar tidak error jika foreign key sudah ada
                try {
                    $table->foreign('id_posyandu')
                          ->references('id_posyandu')
                          ->on('posyandu')
                          ->nullOnDelete();
                } catch (\Exception $e) {
                    // Foreign key sudah ada, skip
                }

                try {
                    $table->foreign('id_posyandu_aktif')
                          ->references('id_posyandu')
                          ->on('posyandu')
                          ->nullOnDelete();
                } catch (\Exception $e) {
                    // Foreign key sudah ada, skip
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pengguna')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->dropForeign(['id_posyandu']);
                $table->dropForeign(['id_posyandu_aktif']);
            });
        }
        Schema::dropIfExists('posyandu');
    }
};