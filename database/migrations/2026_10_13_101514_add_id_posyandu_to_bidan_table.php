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
        Schema::table('bidan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_posyandu')
                ->nullable()
                ->after('id_user');

            $table->foreign('id_posyandu')
                ->references('id_posyandu')
                ->on('posyandu')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('bidan', function (Blueprint $table) {
            $table->dropForeign(['id_posyandu']);
            $table->dropColumn('id_posyandu');
        });
    }
};
