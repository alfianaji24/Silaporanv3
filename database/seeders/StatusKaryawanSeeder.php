<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status_karyawan')->delete();
        
        DB::table('status_karyawan')->insert([
            [
                'kode_status_karyawan' => 'K',
                'nama_status_karyawan' => 'Kontrak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_status_karyawan' => 'T',
                'nama_status_karyawan' => 'Tetap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_status_karyawan' => 'P',
                'nama_status_karyawan' => 'Probation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_status_karyawan' => 'O',
                'nama_status_karyawan' => 'Outsourcing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_status_karyawan' => 'A',
                'nama_status_karyawan' => 'ASN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
