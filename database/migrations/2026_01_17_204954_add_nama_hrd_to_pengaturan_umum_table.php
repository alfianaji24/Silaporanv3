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
        if (!Schema::hasColumn('pengaturan_umum', 'nama_hrd')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                $table->string('nama_hrd')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengaturan_umum', 'nama_hrd')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                $table->dropColumn('nama_hrd');
            });
        }
    }
};
