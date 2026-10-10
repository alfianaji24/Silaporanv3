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
        Schema::table('grup', function (Blueprint $table) {
            $table->char('kode_pola', 5)->nullable()->after('nama_grup');
            $table->date('tanggal_mulai_pola')->nullable()->after('kode_pola');

            $table->foreign('kode_pola')->references('kode_pola')->on('shift_patterns')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grup', function (Blueprint $table) {
            $table->dropForeign(['kode_pola']);
            $table->dropColumn(['kode_pola', 'tanggal_mulai_pola']);
        });
    }
};
