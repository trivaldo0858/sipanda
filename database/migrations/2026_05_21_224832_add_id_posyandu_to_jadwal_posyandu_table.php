<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
{
    Schema::table('jadwal_posyandu', function (Blueprint $table) {
        $table->unsignedBigInteger('id_posyandu')->nullable()->after('id_kader');
    });
}

public function down(): void
{
    Schema::table('jadwal_posyandu', function (Blueprint $table) {
        $table->dropColumn('id_posyandu');
    });
}
};
