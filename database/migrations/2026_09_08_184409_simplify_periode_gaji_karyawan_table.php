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
            // Drop index lama jika ada
            $table->dropIndex(['nik', 'tanggal_mulai', 'tanggal_selesai']);
            
            // Drop kolom tanggal_mulai & tanggal_selesai
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
            
            // Tambahkan unique index nik agar 1 karyawan hanya terdaftar di 1 periode aktif
            $table->unique('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_gaji_karyawan', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->index(['nik', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
