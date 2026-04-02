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
        $adds = [];
        if (!Schema::hasColumn('pengaturan_umum', 'theme_color_1')) $adds[] = 'theme_color_1';
        if (!Schema::hasColumn('pengaturan_umum', 'theme_color_2')) $adds[] = 'theme_color_2';

        if (!empty($adds)) {
            Schema::table('pengaturan_umum', function (Blueprint $table) use ($adds) {
                if (in_array('theme_color_1', $adds, true)) {
                    $table->string('theme_color_1')->nullable()->after('timezone');
                }
                if (in_array('theme_color_2', $adds, true)) {
                    $table->string('theme_color_2')->nullable()->after('theme_color_1');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $drops = [];
        if (Schema::hasColumn('pengaturan_umum', 'theme_color_1')) $drops[] = 'theme_color_1';
        if (Schema::hasColumn('pengaturan_umum', 'theme_color_2')) $drops[] = 'theme_color_2';

        if (!empty($drops)) {
            Schema::table('pengaturan_umum', function (Blueprint $table) use ($drops) {
                $table->dropColumn($drops);
            });
        }
    }
};
