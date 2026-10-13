<?php

use Illuminate\Database\Migrations\Migration;
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

        foreach ($legacyTables as $legacyTable) {
            if (!Schema::hasTable($table) || !Schema::hasTable($legacyTable)) {
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
        $previousTable = 'presensi_jam_kerja';

        if (Schema::hasTable($table) && !Schema::hasTable($previousTable)) {
            Schema::rename($table, $previousTable);
        }
    }
};
