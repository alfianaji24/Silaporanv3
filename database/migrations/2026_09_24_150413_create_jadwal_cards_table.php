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
        Schema::create('jadwal_cards', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jadwal', 100);
            $table->date('dari');
            $table->date('sampai');
            $table->char('kode_cabang', 3)->nullable();
            $table->char('kode_dept', 3)->nullable();
            $table->char('kode_grup', 6)->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('kode_cabang')->references('kode_cabang')->on('cabang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_cards');
    }
};
