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
        // 1. Tabel Master: periode_gaji
        if (!Schema::hasTable('periode_gaji')) {
            Schema::create('periode_gaji', function (Blueprint $table) {
                $table->string('kode_periode_gaji', 10)->primary();
                $table->string('nama_periode', 100);
                $table->unsignedTinyInteger('tanggal_dari')->default(1); // 1-31
                $table->unsignedTinyInteger('tanggal_sampai')->default(31); // 1-31
                $table->unsignedTinyInteger('metode_lintas_bulan')->default(0); // 0=bulan sama, 1=bulan lalu ke saat ini, 2=bulan saat ini ke depan
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tabel Assignment: periode_gaji_karyawan
        if (!Schema::hasTable('periode_gaji_karyawan')) {
            Schema::create('periode_gaji_karyawan', function (Blueprint $table) {
                $table->id();
                $table->string('kode_periode_gaji', 10);
                $table->string('nik', 20);
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('kode_periode_gaji')
                    ->references('kode_periode_gaji')
                    ->on('periode_gaji')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreign('nik')
                    ->references('nik')
                    ->on('karyawan')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->index(['nik', 'is_active']);
                $table->index(['nik', 'tanggal_mulai', 'tanggal_selesai']);
            });
        }

        // 3. Tabel Log Mutasi: mutasi_periode_gaji
        if (!Schema::hasTable('mutasi_periode_gaji')) {
            Schema::create('mutasi_periode_gaji', function (Blueprint $table) {
                $table->id();
                $table->string('nik', 20);
                $table->string('kode_periode_lama', 10)->nullable();
                $table->string('kode_periode_baru', 10);
                $table->date('tanggal_efektif');
                $table->string('keterangan', 255)->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->foreign('nik')
                    ->references('nik')
                    ->on('karyawan')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreign('kode_periode_baru')
                    ->references('kode_periode_gaji')
                    ->on('periode_gaji')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->index('nik');
            });
        }

        // 4. Modifikasi Tabel: slip_gaji
        if (Schema::hasTable('slip_gaji') && !Schema::hasColumn('slip_gaji', 'kode_periode_gaji')) {
            Schema::table('slip_gaji', function (Blueprint $table) {
                $table->string('kode_periode_gaji', 10)->nullable()->after('tahun');

                $table->foreign('kode_periode_gaji')
                    ->references('kode_periode_gaji')
                    ->on('periode_gaji')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('slip_gaji') && Schema::hasColumn('slip_gaji', 'kode_periode_gaji')) {
            Schema::table('slip_gaji', function (Blueprint $table) {
                $table->dropForeign(['kode_periode_gaji']);
                $table->dropColumn('kode_periode_gaji');
            });
        }

        Schema::dropIfExists('mutasi_periode_gaji');
        Schema::dropIfExists('periode_gaji_karyawan');
        Schema::dropIfExists('periode_gaji');
    }
};
