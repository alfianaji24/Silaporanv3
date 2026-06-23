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
        if (!Schema::hasColumn('karyawan', 'npwp')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->string('npwp', 25)->nullable()->after('no_ktp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('karyawan', 'npwp')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn('npwp');
            });
        }
    }
};
