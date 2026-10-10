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
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            $table->boolean('batasi_hari_izin_sakit')->default(false)->after('jml_hari_izin_max');
            $table->integer('jml_hari_izin_sakit_max')->nullable()->after('batasi_hari_izin_sakit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            $table->dropColumn(['batasi_hari_izin_sakit', 'jml_hari_izin_sakit_max']);
        });
    }
};
