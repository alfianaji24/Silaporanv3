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
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('tipe_presensi_in', 20)->default('mobile')->after('status')->comment('fingerprint, mobile, face_recognition');
            $table->string('tipe_presensi_out', 20)->nullable()->after('tipe_presensi_in')->comment('fingerprint, mobile, face_recognition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn(['tipe_presensi_in', 'tipe_presensi_out']);
        });
    }
};
