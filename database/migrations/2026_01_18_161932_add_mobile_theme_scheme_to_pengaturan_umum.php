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
        if (!Schema::hasColumn('pengaturan_umum', 'mobile_theme_scheme')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                $table->string('mobile_theme_scheme')->nullable()->default('green')->after('theme_color_2');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengaturan_umum', 'mobile_theme_scheme')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                $table->dropColumn('mobile_theme_scheme');
            });
        }
    }
};
