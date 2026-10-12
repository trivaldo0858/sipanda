
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('posyandu', 'password_kader')) {
            Schema::table('posyandu', function (Blueprint $table) {
                $table->dropColumn('password_kader');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('posyandu', 'password_kader')) {
            Schema::table('posyandu', function (Blueprint $table) {
                $table->string('password_kader')->nullable();
            });
        }
    }
};