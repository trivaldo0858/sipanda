<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel hanya jika belum ada
        if (! Schema::hasTable('pengguna_posyandu')) {
            Schema::create('pengguna_posyandu', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_posyandu');
                $table->timestamps();

                $table->foreign('id_user')
                      ->references('id_user')
                      ->on('pengguna')
                      ->onDelete('cascade');

                $table->foreign('id_posyandu')
                      ->references('id_posyandu')
                      ->on('posyandu')
                      ->onDelete('cascade');

                $table->unique(['id_user', 'id_posyandu']);
            });
        }

        // Tambah id_posyandu_aktif ke pengguna jika belum ada
        if (! Schema::hasColumn('pengguna', 'id_posyandu_aktif')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->unsignedBigInteger('id_posyandu_aktif')
                      ->nullable()
                      ->after('id_posyandu');

                $table->foreign('id_posyandu_aktif')
                      ->references('id_posyandu')
                      ->on('posyandu')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pengguna', 'id_posyandu_aktif')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->dropForeign(['id_posyandu_aktif']);
                $table->dropColumn('id_posyandu_aktif');
            });
        }

        Schema::dropIfExists('pengguna_posyandu');
    }
};