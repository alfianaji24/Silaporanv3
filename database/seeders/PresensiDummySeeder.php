<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PresensiDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data dummy presensi 5 hari (4 hari ke belakang + hari ini)...');

        // 1. Pastikan Jam Kerja Utama Tersedia
        $jamKerja = Jamkerja::firstOrCreate(
            ['kode_jam_kerja' => 'JK01'],
            [
                'nama_jam_kerja' => 'NON SHIFT',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '16:00:00',
                'istirahat' => '1',
                'jam_awal_istirahat' => '12:00:00',
                'jam_akhir_istirahat' => '13:00:00',
                'total_jam' => 7,
                'lintashari' => '0',
                'keterangan' => 'NORMAL',
            ]
        );

        // Ambil semua karyawan aktif
        $karyawans = Karyawan::where('status_aktif_karyawan', 1)->get();
        if ($karyawans->isEmpty()) {
            $this->command->warn('Tidak ada karyawan aktif ditemukan. Silakan jalankan KaryawanDummySeeder terlebih dahulu.');
            return;
        }

        // Ambil koordinat cabang sebagai basis lokasi GPS presensi
        $cabangMap = Cabang::pluck('lokasi_cabang', 'kode_cabang')->toArray();

        // 2. Tentukan rentang 5 hari (4 hari ke belakang + Hari Ini)
        $today = Carbon::now(config('app.timezone'));
        $dates = [];
        for ($i = 4; $i >= 0; $i--) {
            $dates[] = (clone $today)->subDays($i);
        }

        $totalSeeded = 0;
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalCuti = 0;

        foreach ($dates as $dayIndex => $currentDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $isToday = ($dateStr === $today->format('Y-m-d'));

            $this->command->info("Memproses tanggal: {$dateStr} " . ($isToday ? '(Hari Ini)' : ''));

            foreach ($karyawans as $kIndex => $karyawan) {
                // Gunakan kombinasi deterministik/acak agar tiap hari variasi karyawan terlambat/hadir/izin berganti-ganti secara realistis
                $seedHash = crc32($karyawan->nik . $dateStr);
                $modulo = abs($seedHash) % 100;

                // Lokasi presensi sesuai cabang karyawan (dengan sedikit variasi koordinat mikro)
                $baseLokasi = $cabangMap[$karyawan->kode_cabang] ?? '-7.3430016,108.2169319';
                $lokParts = explode(',', $baseLokasi);
                $lat = floatval(trim($lokParts[0] ?? '-7.3430016')) + ((($seedHash % 50) - 25) * 0.00002);
                $lng = floatval(trim($lokParts[1] ?? '108.2169319')) + ((($seedHash % 50) - 25) * 0.00002);
                $lokasiPresensi = sprintf('%.7f,%.7f', $lat, $lng);

                $status = 'h';
                $jamIn = null;
                $jamOut = null;
                $fotoIn = "{$karyawan->nik}-{$dateStr}-in.png";
                $fotoOut = "{$karyawan->nik}-{$dateStr}-out.png";

                if ($modulo < 70) {
                    // 1. HADIR TEPAT WAKTU (70%) - Jam Masuk: 07:15 s/d 07:58
                    $status = 'h';
                    $jamMasukMenit = rand(15, 58);
                    $jamMasukDetik = rand(10, 59);
                    $jamIn = sprintf('%s 07:%02d:%02d', $dateStr, $jamMasukMenit, $jamMasukDetik);
                    
                    $jamPulangMenit = rand(5, 50);
                    $jamPulangDetik = rand(10, 59);
                    $jamOut = sprintf('%s 16:%02d:%02d', $dateStr, $jamPulangMenit, $jamPulangDetik);

                    $totalHadir++;
                } elseif ($modulo < 88) {
                    // 2. HADIR TERLAMBAT (18%) - Jam Masuk: 08:05 s/d 08:55
                    $status = 'h';
                    $jamMasukMenit = rand(5, 55);
                    $jamMasukDetik = rand(10, 59);
                    $jamIn = sprintf('%s 08:%02d:%02d', $dateStr, $jamMasukMenit, $jamMasukDetik);
                    
                    $jamPulangMenit = rand(10, 59);
                    $jamPulangDetik = rand(10, 59);
                    $jamOut = sprintf('%s 16:%02d:%02d', $dateStr, $jamPulangMenit, $jamPulangDetik);

                    $totalHadir++;
                    $totalTerlambat++;
                } elseif ($modulo < 93) {
                    // 3. IZIN (5%)
                    $status = 'i';
                    $fotoIn = null;
                    $fotoOut = null;
                    $totalIzin++;
                } elseif ($modulo < 97) {
                    // 4. SAKIT (4%)
                    $status = 's';
                    $fotoIn = null;
                    $fotoOut = null;
                    $totalSakit++;
                } else {
                    // 5. CUTI (3%)
                    $status = 'c';
                    $fotoIn = null;
                    $fotoOut = null;
                    $totalCuti++;
                }

                Presensi::updateOrCreate(
                    [
                        'nik' => $karyawan->nik,
                        'tanggal' => $dateStr,
                    ],
                    [
                        'jam_in' => $jamIn,
                        'jam_out' => $jamOut,
                        'foto_in' => $fotoIn,
                        'foto_out' => $fotoOut,
                        'lokasi_in' => ($status === 'h') ? $lokasiPresensi : null,
                        'lokasi_out' => ($status === 'h') ? $lokasiPresensi : null,
                        'kode_jam_kerja' => 'JK01',
                        'status' => $status,
                        'denda' => null,
                        'jam_lembur_aktual' => null,
                        'jam_lembur_netto' => null,
                        'nominal_lembur' => null,
                        'is_lembur_khusus' => 0,
                    ]
                );

                $totalSeeded++;
            }
        }

        $this->command->info("✓ Sukses! Berhasil men-seed {$totalSeeded} record data presensi untuk {$karyawans->count()} karyawan.");
        $this->command->info("Ringkasan 5 Hari:");
        $this->command->info("- Total Hadir: {$totalHadir} (Termasuk {$totalTerlambat} Terlambat & " . ($totalHadir - $totalTerlambat) . " Tepat Waktu)");
        $this->command->info("- Total Izin: {$totalIzin}");
        $this->command->info("- Total Sakit: {$totalSakit}");
        $this->command->info("- Total Cuti: {$totalCuti}");
    }
}
