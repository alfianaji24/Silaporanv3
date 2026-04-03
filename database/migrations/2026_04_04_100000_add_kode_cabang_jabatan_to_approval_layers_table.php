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
        Schema::table('approval_layers', function (Blueprint $table) {
            if (!Schema::hasColumn('approval_layers', 'kode_cabang')) {
                $table->char('kode_cabang', 3)->nullable()->after('kode_dept');
            }
            if (!Schema::hasColumn('approval_layers', 'kode_jabatan')) {
                $table->char('kode_jabatan', 3)->nullable()->after('kode_cabang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_layers', function (Blueprint $table) {
            if (Schema::hasColumn('approval_layers', 'kode_jabatan')) {
                $table->dropColumn('kode_jabatan');
            }
            if (Schema::hasColumn('approval_layers', 'kode_cabang')) {
                $table->dropColumn('kode_cabang');
            }
        });
    }
};