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
        // 1. Tabel Header Periode Jadwal
        Schema::create('jadwal_periode', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode', 100);
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->char('kode_cabang', 3)->nullable();
            $table->char('kode_dept', 3)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('kode_cabang')->references('kode_cabang')->on('cabang')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['bulan', 'tahun', 'status']);
        });

        // 2. Tabel Item Draft Jadwal
        Schema::create('jadwal_periode_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_periode_id');
            $table->char('nik', 10);
            $table->date('tanggal');
            $table->char('kode_jam_kerja', 4)->nullable();
            $table->enum('sumber', ['pattern', 'copy', 'existing', 'manual'])->default('pattern');
            $table->timestamps();

            $table->foreign('jadwal_periode_id')->references('id')->on('jadwal_periode')->onDelete('cascade');
            $table->foreign('nik')->references('nik')->on('karyawan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('presensi_jamkerja')->onDelete('set null')->onUpdate('cascade');
            
            $table->unique(['jadwal_periode_id', 'nik', 'tanggal']);
            $table->index(['nik', 'tanggal']);
        });

        // 3. Tabel Aturan Minimum Personel per Shift
        Schema::create('min_personel_shift', function (Blueprint $table) {
            $table->id();
            $table->char('kode_cabang', 3)->nullable();
            $table->char('kode_dept', 3);
            $table->char('kode_jam_kerja', 4);
            $table->integer('min_personel')->default(1);
            $table->timestamps();

            $table->foreign('kode_cabang')->references('kode_cabang')->on('cabang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('presensi_jamkerja')->onDelete('cascade')->onUpdate('cascade');
            
            $table->unique(['kode_cabang', 'kode_dept', 'kode_jam_kerja'], 'min_personel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('min_personel_shift');
        Schema::dropIfExists('jadwal_periode_items');
        Schema::dropIfExists('jadwal_periode');
    }
};
