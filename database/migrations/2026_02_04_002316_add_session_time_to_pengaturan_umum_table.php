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
        if (!Schema::hasColumn('pengaturan_umum', 'session_time')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                $table->integer('session_time')->nullable()->default(120)->after('mobile_theme_scheme');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengaturan_umum', 'session_time')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                $table->dropColumn('session_time');
            });
        }
    }
};
