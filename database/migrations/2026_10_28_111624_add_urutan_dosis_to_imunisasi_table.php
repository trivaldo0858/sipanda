<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('imunisasi', function (Blueprint $table) {
            $table->unsignedTinyInteger('urutan_dosis')
                ->nullable()
                ->after('id_vaksin');
        });
    }

    public function down(): void
    {
        Schema::table('imunisasi', function (Blueprint $table) {
            $table->dropColumn('urutan_dosis');
        });
    }
};
