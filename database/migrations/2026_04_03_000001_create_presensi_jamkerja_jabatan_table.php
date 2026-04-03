<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('presensi_jamkerja_jabatan')) {
            return;
        }

        Schema::create('presensi_jamkerja_jabatan', function (Blueprint $table) {
            $table->id();
            $table->char('kode_jam_kerja', 4);
            $table->char('kode_jabatan', 3);
            $table->timestamps();

            $table->unique(['kode_jam_kerja', 'kode_jabatan']);
            $table->foreign('kode_jam_kerja')
                ->references('kode_jam_kerja')
                ->on('presensi_jamkerja')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('kode_jabatan')
                ->references('kode_jabatan')
                ->on('jabatan')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_jamkerja_jabatan');
    }
};
