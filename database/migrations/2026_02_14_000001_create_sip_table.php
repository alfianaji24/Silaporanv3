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
        Schema::create('sip', function (Blueprint $table) {
            $table->id();
            $table->string('no_sip', 100)->unique();
            $table->string('nik', 50)->index();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->string('kode_cabang', 50)->nullable()->index();
            $table->string('kode_dept', 50)->nullable()->index();
            $table->string('status_sip', 20)->default('1')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sip');
    }
};
