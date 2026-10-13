<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_jam_kerja', function (Blueprint $table) {
            $table->char('kode_jam_kerja', 4);
            $table->char('kode_jabatan', 3);
            $table->primary(['kode_jam_kerja', 'kode_jabatan']);
            $table->foreign('kode_jam_kerja')
                ->references('kode_jam_kerja')
                ->on('presensi_jamkerja')
                ->cascadeOnDelete();
            $table->foreign('kode_jabatan')
                ->references('kode_jabatan')
                ->on('jabatan')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_jam_kerja');
    }
};
