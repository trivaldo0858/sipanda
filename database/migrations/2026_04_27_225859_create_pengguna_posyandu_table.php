<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel pivot: 1 akun bisa akses banyak posyandu ───────────
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

            // Satu user tidak bisa duplikat posyandu
            $table->unique(['id_user', 'id_posyandu']);
        });

        // ── Tambah kolom posyandu aktif ke tabel pengguna ─────────────
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

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu_aktif']);
            $table->dropColumn('id_posyandu_aktif');
        });

        Schema::dropIfExists('pengguna_posyandu');
    }
};