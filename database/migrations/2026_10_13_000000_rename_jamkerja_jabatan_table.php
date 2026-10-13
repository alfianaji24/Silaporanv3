<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $legacyTable = 'presensi_jamkerja_jabatan';
        $table = 'presensi_jam_kerja';

        if (!Schema::hasTable($table) && Schema::hasTable($legacyTable)) {
            Schema::rename($legacyTable, $table);
            return;
        }

        if (Schema::hasTable($table) && Schema::hasTable($legacyTable)) {
            DB::table($legacyTable)->orderBy('kode_jam_kerja')->orderBy('kode_jabatan')->chunk(500, function ($rows) use ($table) {
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
        $legacyTable = 'presensi_jamkerja_jabatan';
        $table = 'presensi_jam_kerja';

        if (!Schema::hasTable($legacyTable) && Schema::hasTable($table)) {
            Schema::rename($table, $legacyTable);
        }
    }
};
