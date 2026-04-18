<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah id_posyandu ke tabel pengguna
        Schema::table('pengguna', function (Blueprint $table) {
            $table->unsignedBigInteger('id_posyandu')->nullable()->after('role');
            $table->foreign('id_posyandu')
                  ->references('id_posyandu')
                  ->on('posyandu')
                  ->nullOnDelete();
        });

        // Tambah id_posyandu ke tabel kader
        Schema::table('kader', function (Blueprint $table) {
            $table->unsignedBigInteger('id_posyandu')->nullable()->after('wilayah');
            $table->foreign('id_posyandu')
                  ->references('id_posyandu')
                  ->on('posyandu')
                  ->nullOnDelete();
        });

        // Tambah id_posyandu ke tabel bidan
        Schema::table('bidan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_posyandu')->nullable()->after('no_telp');
            $table->foreign('id_posyandu')
                  ->references('id_posyandu')
                  ->on('posyandu')
                  ->nullOnDelete();
        });

        // Update enum role di pengguna — tambah SuperAdmin
        Schema::table('pengguna', function (Blueprint $table) {
            $table->enum('role', ['SuperAdmin', 'Bidan', 'Kader', 'OrangTua'])
                  ->default('OrangTua')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu']);
            $table->dropColumn('id_posyandu');
        });

        Schema::table('kader', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu']);
            $table->dropColumn('id_posyandu');
        });

        Schema::table('bidan', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu']);
            $table->dropColumn('id_posyandu');
        });
    }
};