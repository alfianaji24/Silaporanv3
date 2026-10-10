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
        Schema::table('jadwal_periode', function (Blueprint $table) {
            $table->string('kode_grup', 10)->nullable()->after('kode_dept');
            $table->foreign('kode_grup')->references('kode_grup')->on('grup')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_periode', function (Blueprint $table) {
            $table->dropForeign(['kode_grup']);
            $table->dropColumn('kode_grup');
        });
    }
};
