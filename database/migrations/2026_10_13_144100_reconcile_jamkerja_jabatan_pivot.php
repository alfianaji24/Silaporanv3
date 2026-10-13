<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'presensi_jamkerja_byjab';
        $legacyTables = ['presensi_jam_kerja', 'presensi_jamkerja_jabatan'];

        if (!Schema::hasTable($table)) {
            foreach ($legacyTables as $legacyTable) {
                if (Schema::hasTable($legacyTable)) {
                    Schema::rename($legacyTable, $table);
                    break;
                }
            }
        }

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) {
                $table->char('kode_jam_kerja', 4);
                $table->char('kode_jabatan', 3);
                $table->primary(['kode_jam_kerja', 'kode_jabatan']);
                $table->foreign('kode_jam_kerja')
                    ->references('kode_jam_kerja')
                    ->on('presensi_jamkerja')
                    ->cascadeOnDelete();
                $table->foreign('kode_jabatan')
                    ->references('kode_jabatan')
                    ->on('jabatan')
                    ->cascadeOnDelete();
            });
        }

        foreach ($legacyTables as $legacyTable) {
            if (!Schema::hasTable($legacyTable)) {
                continue;
            }

            DB::table($legacyTable)
                ->orderBy('kode_jam_kerja')
                ->orderBy('kode_jabatan')
                ->chunk(500, function ($rows) use ($table) {
                    foreach ($rows as $row) {
                        DB::table($table)->insertOrIgnore([
                            'kode_jam_kerja' => $row->kode_jam_kerja,
                            'kode_jabatan' => $row->kode_jabatan,
                        ]);
                    }
                });

            Schema::dropIfExists($legacyTable);
        }
    }

    public function down(): void
    {
        $table = 'presensi_jamkerja_byjab';
        $legacyTable = 'presensi_jam_kerja';

        if (!Schema::hasTable($table)) {
            return;
        }

        if (!Schema::hasTable($legacyTable)) {
            Schema::rename($table, $legacyTable);
            return;
        }

        DB::table($table)
            ->orderBy('kode_jam_kerja')
            ->orderBy('kode_jabatan')
            ->chunk(500, function ($rows) use ($legacyTable) {
                foreach ($rows as $row) {
                    DB::table($legacyTable)->insertOrIgnore([
                        'kode_jam_kerja' => $row->kode_jam_kerja,
                        'kode_jabatan' => $row->kode_jabatan,
                    ]);
                }
            });

        Schema::dropIfExists($table);
    }
};
