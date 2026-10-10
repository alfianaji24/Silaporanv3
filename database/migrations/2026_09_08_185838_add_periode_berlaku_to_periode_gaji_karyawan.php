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
        Schema::table('periode_gaji_karyawan', function (Blueprint $table) {
            $table->unsignedTinyInteger('bulan')->nullable()->after('nik'); // 1-12
            $table->year('tahun')->nullable()->after('bulan'); // YYYY
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_gaji_karyawan', function (Blueprint $table) {
            $table->dropColumn(['bulan', 'tahun']);
        });
    }
};
