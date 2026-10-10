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
        Schema::table('min_personel_shift', function (Blueprint $table) {
            if (!Schema::hasColumn('min_personel_shift', 'kode_grup')) {
                $table->string('kode_grup', 10)->nullable()->after('kode_dept');
                $table->foreign('kode_grup')->references('kode_grup')->on('grup')->onDelete('cascade')->onUpdate('cascade');
            }
            
            // Di MySQL, foreign key 'kode_cabang' memakai index 'min_personel_unique'.
            // Maka foreign key kode_cabang harus di-drop terlebih dahulu sebelum index-nya di-drop.
            $table->dropForeign('min_personel_shift_kode_cabang_foreign');
            $table->dropUnique('min_personel_unique');

            // Pasang kembali foreign key kode_cabang dan buat unique constraint baru yang menyertakan kode_grup
            $table->foreign('kode_cabang')->references('kode_cabang')->on('cabang')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['kode_cabang', 'kode_dept', 'kode_grup', 'kode_jam_kerja'], 'min_personel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('min_personel_shift', function (Blueprint $table) {
            $table->dropUnique('min_personel_unique');
            $table->unique(['kode_cabang', 'kode_dept', 'kode_jam_kerja'], 'min_personel_unique');
            $table->dropForeign(['kode_grup']);
            $table->dropColumn('kode_grup');
        });
    }
};
