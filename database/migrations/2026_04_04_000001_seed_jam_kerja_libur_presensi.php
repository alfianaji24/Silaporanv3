<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('presensi_jamkerja')) {
            return;
        }

        DB::table('presensi_jamkerja')->updateOrInsert(
            ['kode_jam_kerja' => 'LBR'],
            [
                'nama_jam_kerja' => 'Libur',
                'jam_masuk' => '00:00:00',
                'jam_pulang' => '00:00:00',
                'istirahat' => '0',
                'jam_awal_istirahat' => null,
                'jam_akhir_istirahat' => null,
                'total_jam' => 0,
                'lintashari' => '0',
                'keterangan' => 'Jam kerja sistem: libur (jadwal per karyawan / tanggal).',
                'color' => '#6c757d',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('presensi_jamkerja')) {
            return;
        }

        DB::table('presensi_jamkerja')->where('kode_jam_kerja', 'LBR')->delete();
    }
};
