<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posyandu', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('nama_posyandu');
            $table->string('kecamatan')->nullable()->after('wilayah');
            $table->string('kabupaten_kota')->nullable()->after('kecamatan');
            $table->string('nama_koordinator')->nullable()->after('kabupaten_kota');
            $table->string('email')->nullable()->after('no_telp');
        });
    }

    public function down(): void
    {
        Schema::table('posyandu', function (Blueprint $table) {
            $table->dropColumn([
                'kategori',
                'kecamatan',
                'kabupaten_kota',
                'nama_koordinator',
                'email',
            ]);
        });
    }
};
