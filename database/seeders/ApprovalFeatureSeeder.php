<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Features
        $features = [
            ['feature' => 'IZIN_ABSEN', 'name' => 'Izin Absen', 'description' => 'Izin Tidak Masuk Kerja'],
            ['feature' => 'IZIN_SAKIT', 'name' => 'Izin Sakit', 'description' => 'Izin Sakit dengan Surat Dokter'],
            ['feature' => 'IZIN_CUTI', 'name' => 'Izin Cuti', 'description' => 'Pengajuan Cuti Tahunan'],
            ['feature' => 'IZIN_DINAS', 'name' => 'Izin Dinas', 'description' => 'Perjalanan Dinas Luar Kota'],
            ['feature' => 'LEMBUR', 'name' => 'Lembur', 'description' => 'Pengajuan Lembur Kerja'],
        ];

        foreach ($features as $f) {
            DB::table('approval_features')->updateOrInsert(
                ['feature' => $f['feature']],
                $f
            );
        }
    }
}
