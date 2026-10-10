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
        Schema::create('shift_pattern_details', function (Blueprint $table) {
            $table->id();
            $table->char('kode_pola', 5);
            $table->integer('urutan_hari');
            $table->char('kode_jam_kerja', 4)->nullable();
            $table->timestamps();

            $table->foreign('kode_pola')->references('kode_pola')->on('shift_patterns')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('presensi_jamkerja')->onDelete('set null')->onUpdate('cascade');
            $table->unique(['kode_pola', 'urutan_hari']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_pattern_details');
    }
};
