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
        Schema::table('slip_gaji', function (Blueprint $table) {
            $table->string('kode_slip_gaji', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slip_gaji', function (Blueprint $table) {
            $table->string('kode_slip_gaji', 8)->change();
        });
    }
};
