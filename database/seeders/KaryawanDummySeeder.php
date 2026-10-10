<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Statuskaryawan;
use App\Models\Statuskawin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KaryawanDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data master dan 100 data dummy karyawan...');

        // 1. Pastikan Master Cabang Tersedia
        $cabangs = [
            [
                'kode_cabang' => 'TSM',
                'nama_cabang' => 'Tasikmalaya',
                'alamat_cabang' => 'Jln. Perintis Kemerdekaan No. 27 Kawalu Tasikmalaya',
                'telepon_cabang' => '0265311766',
                'lokasi_cabang' => '-7.343001685576052, 108.21693193717599',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
            [
                'kode_cabang' => 'BGR',
                'nama_cabang' => 'Bogor',
                'alamat_cabang' => 'Jln. Raya Pajajaran No. 27 Kota Bogor',
                'telepon_cabang' => '0251832100',
                'lokasi_cabang' => '-6.7272704,108.5341696',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
            [
                'kode_cabang' => 'BDG',
                'nama_cabang' => 'Bandung',
                'alamat_cabang' => 'Jl. Asia Afrika No. 100, Bandung',
                'telepon_cabang' => '0224201000',
                'lokasi_cabang' => '-6.917464,107.619122',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
            [
                'kode_cabang' => 'JKT',
                'nama_cabang' => 'Jakarta',
                'alamat_cabang' => 'Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan',
                'telepon_cabang' => '0215701000',
                'lokasi_cabang' => '-6.208763,106.845599',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
            [
                'kode_cabang' => 'SBY',
                'nama_cabang' => 'Surabaya',
                'alamat_cabang' => 'Jl. Pemuda No. 45, Surabaya',
                'telepon_cabang' => '0315311000',
                'lokasi_cabang' => '-7.265757,112.750373',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
            [
                'kode_cabang' => 'SMG',
                'nama_cabang' => 'Semarang',
                'alamat_cabang' => 'Jl. Pandanaran No. 58, Semarang',
                'telepon_cabang' => '0248412000',
                'lokasi_cabang' => '-6.993200,110.420300',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
            [
                'kode_cabang' => 'YOG',
                'nama_cabang' => 'Yogyakarta',
                'alamat_cabang' => 'Jl. Malioboro No. 88, Yogyakarta',
                'telepon_cabang' => '0274512000',
                'lokasi_cabang' => '-7.795600,110.369500',
                'radius_cabang' => 30,
                'timezone' => 'Asia/Jakarta',
            ],
        ];

        foreach ($cabangs as $cabang) {
            Cabang::updateOrCreate(['kode_cabang' => $cabang['kode_cabang']], $cabang);
        }

        // 2. Pastikan Master Departemen Tersedia
        $departemens = [
            ['kode_dept' => 'ICT', 'nama_dept' => 'Information Technology'],
            ['kode_dept' => 'AKT', 'nama_dept' => 'Akuntansi & Keuangan'],
            ['kode_dept' => 'SDM', 'nama_dept' => 'Sumber Daya Manusia (HRD)'],
            ['kode_dept' => 'MKT', 'nama_dept' => 'Marketing & Penjualan'],
            ['kode_dept' => 'PRD', 'nama_dept' => 'Produksi & Operasional'],
            ['kode_dept' => 'LOG', 'nama_dept' => 'Logistik & Supply Chain'],
            ['kode_dept' => 'GA',  'nama_dept' => 'General Affairs & Fasilitas'],
        ];

        foreach ($departemens as $dept) {
            Departemen::updateOrCreate(['kode_dept' => $dept['kode_dept']], $dept);
        }

        // 3. Pastikan Master Jabatan Tersedia
        $jabatans = [
            ['kode_jabatan' => 'J01', 'nama_jabatan' => 'Direktur / General Manager'],
            ['kode_jabatan' => 'J02', 'nama_jabatan' => 'Manager Departemen'],
            ['kode_jabatan' => 'J03', 'nama_jabatan' => 'Assistant Manager'],
            ['kode_jabatan' => 'J04', 'nama_jabatan' => 'Supervisor'],
            ['kode_jabatan' => 'J05', 'nama_jabatan' => 'Team Leader'],
            ['kode_jabatan' => 'J06', 'nama_jabatan' => 'Senior Staff / Specialist'],
            ['kode_jabatan' => 'J07', 'nama_jabatan' => 'Staff Pelaksana'],
            ['kode_jabatan' => 'J08', 'nama_jabatan' => 'Junior Staff'],
            ['kode_jabatan' => 'J09', 'nama_jabatan' => 'Operator / Teknisi'],
            ['kode_jabatan' => 'J10', 'nama_jabatan' => 'Magang / Trainee'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(['kode_jabatan' => $jabatan['kode_jabatan']], $jabatan);
        }

        // 4. Pastikan Master Status Karyawan Tersedia
        $statusKaryawans = [
            ['kode_status_karyawan' => 'T', 'nama_status_karyawan' => 'Tetap'],
            ['kode_status_karyawan' => 'K', 'nama_status_karyawan' => 'Kontrak'],
            ['kode_status_karyawan' => 'P', 'nama_status_karyawan' => 'Probation (Percobaan)'],
            ['kode_status_karyawan' => 'M', 'nama_status_karyawan' => 'Magang / Internship'],
            ['kode_status_karyawan' => 'F', 'nama_status_karyawan' => 'Freelance / Harian Lepas'],
            ['kode_status_karyawan' => 'O', 'nama_status_karyawan' => 'Outsourcing (Alih Daya)'],
        ];

        foreach ($statusKaryawans as $sk) {
            Statuskaryawan::updateOrCreate(['kode_status_karyawan' => $sk['kode_status_karyawan']], $sk);
        }

        // 5. Pastikan Master Status Kawin Tersedia
        $statusKawins = [
            ['kode_status_kawin' => 'TK', 'status_kawin' => 'Tidak Kawin', 'kategori_ter' => 'A', 'nilai_ptkp' => 54000000],
            ['kode_status_kawin' => 'K0', 'status_kawin' => 'Kawin Belum Punya Tanggungan', 'kategori_ter' => 'A', 'nilai_ptkp' => 58500000],
            ['kode_status_kawin' => 'K1', 'status_kawin' => 'Kawin Punya Tanggungan 1', 'kategori_ter' => 'B', 'nilai_ptkp' => 63000000],
            ['kode_status_kawin' => 'K2', 'status_kawin' => 'Kawin Punya Tanggungan 2', 'kategori_ter' => 'B', 'nilai_ptkp' => 67500000],
            ['kode_status_kawin' => 'K3', 'status_kawin' => 'Kawin Punya Tanggungan 3', 'kategori_ter' => 'C', 'nilai_ptkp' => 72000000],
            ['kode_status_kawin' => 'HB', 'status_kawin' => 'Telah Berpisah Secara Hukum / Janda / Duda', 'kategori_ter' => 'A', 'nilai_ptkp' => 54000000],
        ];

        foreach ($statusKawins as $skw) {
            Statuskawin::updateOrCreate(['kode_status_kawin' => $skw['kode_status_kawin']], $skw);
        }

        // 6. Dataset TEPAT 100 Karyawan dengan Variasi Lengkap
        // Komposisi Status: Tetap (35), Kontrak (30), Probation (10), Magang (10), Outsourcing (10), Freelance (5) = 100
        // Komposisi Pendidikan: S3 (2), S2 (6), S1 (32), D4 (5), D3 (15), D2 (2), D1 (2), SMK (18), SMA (12), SMP (4), SD (2) = 100
        $rawEmployees = [
            // [1-8] S3 & S2
            ['Prof. Dr. Hendra Gunawan, S.Kom., M.T.', 'L', 'S3', 'Ilmu Komputer', 'T', 'J01', 'ICT', 'JKT', 'K2', 1978, 'BCA'],
            ['Dr. Maya Kartika Sari, S.E., M.M.', 'P', 'S3', 'Ilmu Manajemen', 'F', 'J01', 'AKT', 'JKT', 'K1', 1980, 'Mandiri'],
            ['Agus Tri Wicaksono, M.Kom.', 'L', 'S2', 'Magister Teknik Informatika', 'T', 'J02', 'ICT', 'BDG', 'K2', 1985, 'BCA'],
            ['Ratna Dewi Anggraeni, M.M.', 'P', 'S2', 'Magister Manajemen', 'T', 'J02', 'SDM', 'JKT', 'K1', 1987, 'BNI'],
            ['Bambang Trihatmodjo, M.Ak.', 'L', 'S2', 'Magister Akuntansi', 'T', 'J02', 'AKT', 'SBY', 'K3', 1983, 'Mandiri'],
            ['Farida Nurul Aini, M.I.Kom.', 'P', 'S2', 'Magister Ilmu Komunikasi', 'T', 'J02', 'MKT', 'BDG', 'K0', 1989, 'BCA'],
            ['Ir. Dedi Kurniawan, M.T.', 'L', 'S2', 'Magister Teknik Industri', 'T', 'J02', 'PRD', 'TSM', 'K2', 1984, 'BRI'],
            ['Siti Khodijah, M.Kn.', 'P', 'S2', 'Magister Kenotariatan', 'K', 'J03', 'GA', 'JKT', 'K1', 1988, 'BSI'],

            // [9-37] S1 (Tetap, Kontrak, Probation, Magang, Freelance)
            ['Muhammad Fauzan Rahman', 'L', 'S1', 'Teknik Informatika', 'T', 'J03', 'ICT', 'TSM', 'K1', 1991, 'BCA'],
            ['Indah Puspitasari', 'P', 'S1', 'Sistem Informasi', 'T', 'J04', 'ICT', 'BGR', 'K0', 1993, 'Mandiri'],
            ['Rizky Aditya Pratama', 'L', 'S1', 'Teknik Komputer', 'T', 'J06', 'ICT', 'JKT', 'TK', 1995, 'BCA'],
            ['Anisa Rahmawati', 'P', 'S1', 'Rekayasa Perangkat Lunak', 'K', 'J07', 'ICT', 'BDG', 'TK', 1997, 'BSI'],
            ['Dimas Bayu Prasetyo', 'L', 'S1', 'Cyber Security', 'K', 'J07', 'ICT', 'SBY', 'TK', 1998, 'Mandiri'],
            ['Kevin Jonathan', 'L', 'S1', 'Teknik Informatika', 'P', 'J08', 'ICT', 'JKT', 'TK', 1999, 'BCA'],
            ['Fajar Nugraha', 'L', 'S1', 'Akuntansi', 'T', 'J03', 'AKT', 'TSM', 'K2', 1990, 'BRI'],
            ['Citra Lestari Wulandari', 'P', 'S1', 'Manajemen Keuangan', 'T', 'J04', 'AKT', 'BGR', 'K1', 1992, 'Mandiri'],
            ['Budi Santoso', 'L', 'S1', 'Akuntansi Perpajakan', 'T', 'J06', 'AKT', 'BDG', 'K1', 1994, 'BCA'],
            ['Devi Anggraini', 'P', 'S1', 'Perbankan & Keuangan', 'K', 'J07', 'AKT', 'JKT', 'TK', 1996, 'BNI'],
            ['Rendi Andika', 'L', 'S1', 'Ekonomi Pembangunan', 'K', 'J07', 'AKT', 'SMG', 'TK', 1997, 'BRI'],
            ['Yuliana Safitri', 'P', 'S1', 'Akuntansi Audit', 'P', 'J08', 'AKT', 'YOG', 'TK', 2000, 'BSI'],
            ['Ahmad Hidayat', 'L', 'S1', 'Psikologi', 'T', 'J03', 'SDM', 'JKT', 'K2', 1989, 'BCA'],
            ['Nadia Putri Utami', 'P', 'S1', 'Manajemen SDM', 'T', 'J04', 'SDM', 'BDG', 'K1', 1993, 'Mandiri'],
            ['Guruh Prasetya', 'L', 'S1', 'Ilmu Hukum', 'T', 'J06', 'SDM', 'BGR', 'K0', 1994, 'BNI'],
            ['Lia Novitasari', 'P', 'S1', 'Administrasi Bisnis', 'K', 'J07', 'SDM', 'TSM', 'TK', 1998, 'BRI'],
            ['Taufik Hidayat', 'L', 'S1', 'Manajemen Bisnis', 'K', 'J07', 'SDM', 'SBY', 'TK', 1996, 'Mandiri'],
            ['Eka Wulandari', 'P', 'S1', 'Ilmu Komunikasi', 'T', 'J03', 'MKT', 'JKT', 'K1', 1991, 'BCA'],
            ['Rian Firmansyah', 'L', 'S1', 'Manajemen Pemasaran', 'T', 'J04', 'MKT', 'SBY', 'K1', 1992, 'Mandiri'],
            ['Siti Aisyah Putri', 'P', 'S1', 'Digital Marketing', 'K', 'J06', 'MKT', 'BDG', 'TK', 1996, 'BCA'],
            ['Bagus Wicaksono', 'L', 'S1', 'Hubungan Masyarakat', 'K', 'J07', 'MKT', 'SMG', 'TK', 1997, 'BNI'],
            ['Vina Melinda', 'P', 'S1', 'Desain Komunikasi Visual', 'K', 'J07', 'MKT', 'YOG', 'TK', 1998, 'BSI'],
            ['Doni Setiawan', 'L', 'S1', 'Teknik Industri', 'T', 'J03', 'PRD', 'TSM', 'K2', 1988, 'BRI'],
            ['Rina Marlina', 'P', 'S1', 'Teknik Kimia', 'T', 'J04', 'PRD', 'BGR', 'K1', 1992, 'Mandiri'],
            ['Wahyu Saputra', 'L', 'S1', 'Teknik Mesin', 'T', 'J06', 'PRD', 'BDG', 'K1', 1993, 'BCA'],
            ['Hendra Kusuma', 'L', 'S1', 'Manajemen Operasional', 'K', 'J07', 'PRD', 'SBY', 'TK', 1996, 'Mandiri'],
            ['Zainal Abidin', 'L', 'S1', 'Manajemen Logistik', 'T', 'J04', 'LOG', 'JKT', 'K2', 1990, 'BCA'],
            ['Nisa Amelia', 'P', 'S1', 'Supply Chain Management', 'K', 'J06', 'LOG', 'TSM', 'TK', 1995, 'BRI'],
            ['Arif Munandar', 'L', 'S1', 'Teknik Sipil', 'T', 'J04', 'GA', 'BDG', 'K1', 1991, 'Mandiri'],

            // [38-42] D4
            ['Rahmat Hidayatullah', 'L', 'D4', 'Teknik Informatika Terapan', 'T', 'J05', 'ICT', 'BDG', 'K1', 1993, 'BCA'],
            ['Fitri Handayani', 'P', 'D4', 'Akuntansi Manajerial', 'T', 'J05', 'AKT', 'BGR', 'K0', 1994, 'Mandiri'],
            ['Agung Permana', 'L', 'D4', 'Logistik Bisnis Terapan', 'T', 'J05', 'LOG', 'JKT', 'K1', 1992, 'BNI'],
            ['Dewi Sartika', 'P', 'D4', 'Manajemen Bisnis Terapan', 'K', 'J06', 'MKT', 'SBY', 'TK', 1996, 'BCA'],
            ['Lukman Hakim', 'L', 'D4', 'Teknologi Rekayasa Otomasi', 'K', 'J06', 'PRD', 'TSM', 'K1', 1995, 'BRI'],

            // [43-55] D3 (Tetap, Kontrak, Probation, Outsourcing, Freelance)
            ['Alamsyah Putra', 'L', 'D3', 'Manajemen Informatika', 'T', 'J06', 'ICT', 'TSM', 'K1', 1993, 'BCA'],
            ['Nurul Hasanah', 'P', 'D3', 'Komputerisasi Akuntansi', 'T', 'J06', 'AKT', 'TSM', 'K1', 1994, 'BRI'],
            ['Gani Firmansyah', 'L', 'D3', 'Teknik Elektro', 'T', 'J05', 'PRD', 'BGR', 'K2', 1991, 'Mandiri'],
            ['Tia Rosalina', 'P', 'D3', 'Sekretari & Administrasi', 'T', 'J07', 'SDM', 'BDG', 'K0', 1995, 'BCA'],
            ['Hadi Prasetyo', 'L', 'D3', 'Perpajakan', 'K', 'J07', 'AKT', 'JKT', 'TK', 1997, 'BNI'],
            ['Siska Rahmawati', 'P', 'D3', 'Hubungan Masyarakat', 'K', 'J07', 'MKT', 'SBY', 'TK', 1998, 'Mandiri'],
            ['Miftah Fauzi', 'L', 'D3', 'Teknik Mesin Otomotif', 'K', 'J07', 'PRD', 'SMG', 'TK', 1996, 'BRI'],
            ['Ika Nurjanah', 'P', 'D3', 'Keuangan & Perbankan', 'K', 'J07', 'AKT', 'YOG', 'TK', 1997, 'BSI'],
            ['Rio Febrian', 'L', 'D3', 'Teknik Jaringan Komputer', 'K', 'J08', 'ICT', 'BGR', 'TK', 1999, 'BCA'],
            ['Yudi Santika', 'L', 'D3', 'Manajemen Logistik', 'O', 'J07', 'LOG', 'JKT', 'K1', 1994, 'Mandiri'],
            ['Lina Karlina', 'P', 'D3', 'Administrasi Perkantoran', 'P', 'J08', 'SDM', 'TSM', 'TK', 2001, 'BRI'],
            ['Ferry Irawan', 'L', 'D3', 'Desain Grafis Multimedia', 'F', 'J07', 'MKT', 'BDG', 'TK', 1996, 'BCA'],
            ['Danang Joyo', 'L', 'D3', 'Teknik Pengelasan & Fabrikasi', 'T', 'J05', 'PRD', 'TSM', 'K2', 1990, 'BRI'],

            // [56-59] D2 & D1
            ['Ilham Ramadhan', 'L', 'D2', 'Administrasi Pergudangan', 'O', 'J08', 'LOG', 'BGR', 'K1', 1996, 'Mandiri'],
            ['Kartika Sari', 'P', 'D2', 'Kesekretariatan Praktis', 'K', 'J08', 'GA', 'TSM', 'TK', 1999, 'BRI'],
            ['Rian Gunawan', 'L', 'D1', 'Aplikasi Komputer Bisnis', 'O', 'J09', 'ICT', 'TSM', 'TK', 1998, 'BCA'],
            ['Desi Ratnasari', 'P', 'D1', 'Layanan Pelanggan', 'K', 'J08', 'GA', 'BDG', 'TK', 2000, 'BNI'],

            // [60-74] SMK (Tetap, Kontrak, Probation, Outsourcing, Freelance)
            ['Asep Sunandar', 'L', 'SMK', 'Teknik Komputer & Jaringan', 'T', 'J07', 'ICT', 'TSM', 'K2', 1994, 'BRI'],
            ['Dadan Ramdani', 'L', 'SMK', 'Rekayasa Perangkat Lunak', 'K', 'J08', 'ICT', 'TSM', 'TK', 1999, 'BCA'],
            ['Cecep Saepudin', 'L', 'SMK', 'Teknik Pemesinan', 'T', 'J09', 'PRD', 'TSM', 'K3', 1991, 'BRI'],
            ['Encep Suryana', 'L', 'SMK', 'Teknik Kendaraan Ringan', 'T', 'J09', 'PRD', 'BGR', 'K2', 1993, 'Mandiri'],
            ['Ai Maryani', 'P', 'SMK', 'Akuntansi Keuangan Lembaga', 'T', 'J07', 'AKT', 'TSM', 'K1', 1995, 'BRI'],
            ['Neng Eli Siti', 'P', 'SMK', 'Otomatisasi Tata Kelola Perkantoran', 'K', 'J08', 'SDM', 'TSM', 'TK', 2000, 'BCA'],
            ['Jajang Nurjaman', 'L', 'SMK', 'Teknik Pengelasan', 'O', 'J09', 'PRD', 'BDG', 'K1', 1995, 'Mandiri'],
            ['Ujang Suherman', 'L', 'SMK', 'Teknik Fabrikasi Logam', 'O', 'J09', 'PRD', 'JKT', 'K2', 1993, 'BNI'],
            ['Novi Sri Wahyuni', 'P', 'SMK', 'Bisnis Daring & Pemasaran', 'K', 'J08', 'MKT', 'BDG', 'TK', 2001, 'BCA'],
            ['Rahmat Mulyana', 'L', 'SMK', 'Multimedia & Grafis', 'F', 'J08', 'MKT', 'YOG', 'TK', 2000, 'BSI'],
            ['Cucun Cuhendar', 'L', 'SMK', 'Teknik Logistik & Distribusi', 'K', 'J09', 'LOG', 'TSM', 'TK', 1999, 'BRI'],
            ['Lilis Suryani', 'P', 'SMK', 'Perbankan Syariah', 'K', 'J08', 'AKT', 'TSM', 'TK', 2001, 'BSI'],
            ['Gugun Gunawan', 'L', 'SMK', 'Teknik Audio Video', 'P', 'J09', 'PRD', 'TSM', 'TK', 2002, 'BRI'],
            ['Dini Indriani', 'P', 'SMK', 'Administrasi Perkantoran', 'P', 'J08', 'GA', 'BGR', 'TK', 2002, 'BCA'],
            ['Rizal Fahmi', 'L', 'SMK', 'Teknik Mekatronika', 'P', 'J09', 'PRD', 'BDG', 'TK', 2001, 'BCA'],

            // [75-84] SMA (Tetap, Kontrak, Probation, Outsourcing, Freelance)
            ['Dadang Koswara', 'L', 'SMA', 'IPA', 'T', 'J09', 'PRD', 'TSM', 'K3', 1988, 'BRI'],
            ['Yayan Sofyan', 'L', 'SMA', 'IPS', 'T', 'J09', 'LOG', 'BGR', 'K2', 1990, 'Mandiri'],
            ['Endang Sujana', 'L', 'SMA', 'IPS', 'O', 'J09', 'GA', 'BDG', 'K2', 1991, 'BRI'],
            ['Agus Salim', 'L', 'SMA', 'IPA', 'T', 'J07', 'LOG', 'TSM', 'K2', 1992, 'BRI'],
            ['Sri Rejeki', 'P', 'SMA', 'Bahasa & Sastra', 'K', 'J08', 'GA', 'JKT', 'K1', 1996, 'BCA'],
            ['Solihin bin Syarif', 'L', 'SMA', 'IPS', 'O', 'J09', 'GA', 'TSM', 'K1', 1994, 'BRI'],
            ['Hasan Basri', 'L', 'SMA', 'IPA', 'K', 'J09', 'LOG', 'SBY', 'TK', 1998, 'Mandiri'],
            ['Kokom Komariah', 'P', 'SMA', 'IPS', 'O', 'J09', 'PRD', 'TSM', 'K1', 1995, 'BRI'],
            ['Deden Mukhlis', 'L', 'SMA', 'IPS', 'P', 'J09', 'GA', 'BGR', 'TK', 2001, 'Mandiri'],
            ['Joni Iskandar', 'L', 'SMA', 'IPS', 'F', 'J09', 'LOG', 'JKT', 'TK', 1999, 'BCA'],

            // [85-88] SMP
            ['Oding Supriatna', 'L', 'SMP', 'Pendidikan Menengah Pertama', 'T', 'J09', 'GA', 'TSM', 'K3', 1985, 'BRI'],
            ['Mamat Rahmatullah', 'L', 'SMP', 'Pendidikan Menengah Pertama', 'O', 'J09', 'GA', 'BGR', 'K2', 1987, 'Mandiri'],
            ['Tatang Sutisna', 'L', 'SMP', 'Pendidikan Menengah Pertama', 'T', 'J09', 'PRD', 'TSM', 'K3', 1989, 'BRI'],
            ['Maman Supratman', 'L', 'SMP', 'Pendidikan Menengah Pertama', 'P', 'J09', 'PRD', 'SBY', 'K2', 1992, 'Mandiri'],

            // [91-92] SD
            ['Saripudin bin Aceng', 'L', 'SD', 'Pendidikan Dasar', 'T', 'J09', 'GA', 'TSM', 'K3', 1982, 'BRI'],
            ['Kadir Sulaeman', 'L', 'SD', 'Pendidikan Dasar', 'O', 'J09', 'GA', 'BDG', 'K3', 1984, 'BRI'],

            // [93-100] MAGANG / INTERNSHIP (10 Karyawan Magang dari berbagai jenjang: S1, D3, SMK)
            ['Gilang Pratama Putra', 'L', 'S1', 'Teknik Informatika (Semester 7)', 'M', 'J10', 'ICT', 'TSM', 'TK', 2003, 'BCA'],
            ['Salma Nabilla', 'P', 'S1', 'Sistem Informasi (Semester 7)', 'M', 'J10', 'ICT', 'BDG', 'TK', 2003, 'Mandiri'],
            ['Fikri Zulfikar', 'L', 'S1', 'Akuntansi Keuangan (Semester 6)', 'M', 'J10', 'AKT', 'JKT', 'TK', 2004, 'BSI'],
            ['Aditya Nugroho', 'L', 'D3', 'Teknik Komputer (Semester 5)', 'M', 'J10', 'ICT', 'TSM', 'TK', 2004, 'BRI'],
            ['Hanifah Khairunnisa', 'P', 'D3', 'Administrasi Bisnis (Semester 5)', 'M', 'J10', 'GA', 'TSM', 'TK', 2004, 'BRI'],
            ['Rizky Maulana', 'L', 'SMK', 'Teknik Komputer & Jaringan', 'M', 'J10', 'ICT', 'TSM', 'TK', 2006, 'BCA'],
            ['Dini Nur Anggraeni', 'P', 'SMK', 'Akuntansi Keuangan Lembaga', 'M', 'J10', 'AKT', 'TSM', 'TK', 2006, 'BRI'],
            ['Faiz Al-Ghifari', 'L', 'SMK', 'Teknik Pemesinan Industri', 'M', 'J10', 'PRD', 'TSM', 'TK', 2006, 'Mandiri'],

            // [Dua Magang Tambahan untuk genap 10 Magang & 100 Total]
            ['Zahra Amelia', 'P', 'S1', 'Psikologi SDM (Semester 6)', 'M', 'J10', 'SDM', 'BGR', 'TK', 2004, 'BNI'],
            ['Arya Wijaya', 'L', 'S1', 'Ilmu Komunikasi (Semester 6)', 'M', 'J10', 'MKT', 'BDG', 'TK', 2004, 'BCA'],
        ];

        // 7. Seed 100 Karyawan ke Database
        $defaultPassword = Hash::make('12345');
        $kotaMap = [
            'TSM' => ['Tasikmalaya', 'Ciamis', 'Garut'],
            'BGR' => ['Bogor', 'Sukabumi', 'Depok'],
            'BDG' => ['Bandung', 'Cimahi', 'Sumedang'],
            'JKT' => ['Jakarta Selatan', 'Jakarta Pusat', 'Jakarta Barat'],
            'SBY' => ['Surabaya', 'Sidoarjo', 'Gresik'],
            'SMG' => ['Semarang', 'Kendal', 'Salatiga'],
            'YOG' => ['Yogyakarta', 'Sleman', 'Bantul'],
        ];

        $jalanList = [
            'Jl. Merdeka No. ', 'Jl. Sudirman No. ', 'Jl. Diponegoro No. ', 'Jl. Perintis No. ',
            'Jl. Asia Afrika No. ', 'Jl. Gatot Subroto No. ', 'Jl. Ahmad Yani No. ', 'Jl. Pahlawan No. ',
            'Jl. Sukarno Hatta No. ', 'Jl. RE Martadinata No. ', 'Jl. Juanda No. ', 'Jl. Cihampelas No. '
        ];

        $daruratHubungan = ['Istri', 'Suami', 'Ayah', 'Ibu', 'Kakak Kandung', 'Adik Kandung'];

        $countSeeded = 0;
        $totalTarget = count($rawEmployees);

        for ($i = 0; $i < $totalTarget; $i++) {
            $emp = $rawEmployees[$i];
            $indexNumber = $i + 1;

            // Generate NIK 9 Digit: '2601' + 5 digit urut (e.g., 260100001 s/d 260100100)
            $nik = sprintf('2601%05d', $indexNumber);
            $nikShow = sprintf('26.01.%05d', $indexNumber);

            $nama = $emp[0];
            $jk = $emp[1];
            $pendidikan = $emp[2];
            $jurusan = $emp[3];
            $statusKaryawan = $emp[4];
            $kodeJabatan = $emp[5];
            $kodeDept = $emp[6];
            $kodeCabang = $emp[7];
            $statusKawin = $emp[8];
            $birthYear = $emp[9];
            $bankName = $emp[10];

            // Tanggal Lahir
            $birthMonth = str_pad(($indexNumber % 12) + 1, 2, '0', STR_PAD_LEFT);
            $birthDay = str_pad(($indexNumber % 28) + 1, 2, '0', STR_PAD_LEFT);
            $tanggalLahir = "{$birthYear}-{$birthMonth}-{$birthDay}";

            // Tempat Lahir & Alamat (max 20 chars untuk tempat_lahir)
            $kotaOptions = $kotaMap[$kodeCabang] ?? ['Tasikmalaya'];
            $tempatLahir = $kotaOptions[$indexNumber % count($kotaOptions)];
            $jalan = $jalanList[$indexNumber % count($jalanList)] . rand(1, 150);
            $alamat = "{$jalan}, {$tempatLahir}";

            // KTP (Tepat 16 Digit) & NPWP (Tepat 20 Karakter Terformat)
            $ktpPrefix = ($kodeCabang === 'JKT') ? '3171' : (($kodeCabang === 'SBY') ? '3578' : (($kodeCabang === 'SMG') ? '3374' : (($kodeCabang === 'YOG') ? '3471' : '3207')));
            $noKtp = sprintf('%s%02d%02d%02d%04d', $ktpPrefix, ($indexNumber % 12) + 1, ($indexNumber % 28) + 1, $birthYear % 100, $indexNumber);
            $npwp = sprintf('%02d.%03d.%03d.%d-%03d.000', ($indexNumber % 90) + 10, rand(100, 999), rand(100, 999), rand(1, 9), rand(100, 999));

            // No HP (Tepat <= 15 Digit) & Email
            $hpPrefixes = ['0812', '0813', '0821', '0857', '0878', '0896'];
            $hpPrefix = $hpPrefixes[$indexNumber % count($hpPrefixes)];
            $noHp = $hpPrefix . sprintf('%04d%04d', $indexNumber, rand(1000, 9999));

            // Clean nama untuk email & rekening
            $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $nama);
            $nameParts = array_values(array_filter(explode(' ', strtolower(trim($cleanName)))));
            $emailUsername = count($nameParts) > 1 ? $nameParts[0] . '.' . end($nameParts) : $nameParts[0] . $indexNumber;
            $email = $emailUsername . '@company.id';

            // Kontak Darurat
            $kontakDaruratHp = '0812' . sprintf('%08d', rand(10000000, 99999999));
            $hubunganDarurat = $daruratHubungan[$indexNumber % count($daruratHubungan)];

            // Bank Rekening
            $noRekening = sprintf('%010d', rand(1000000000, 2147483647));

            // Tanggal Masuk (antara 2018 - 2026)
            $joinYear = max(2018, min(2026, $birthYear + 22));
            $joinMonth = str_pad(($indexNumber % 12) + 1, 2, '0', STR_PAD_LEFT);
            $tanggalMasuk = "{$joinYear}-{$joinMonth}-01";

            // Status Aktif: 96 Aktif, 4 Non-Aktif (Resign/Pensiun) untuk simulasi
            $isAktif = ($indexNumber > 96) ? '0' : '1';
            $tanggalNonaktif = ($isAktif === '0') ? '2026-08-31' : null;

            Karyawan::updateOrCreate(
                ['nik' => $nik],
                [
                    'nik_show' => $nikShow,
                    'no_ktp' => $noKtp,
                    'npwp' => $npwp,
                    'hitung_pph21' => 1,
                    'nama_karyawan' => $nama,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $alamat,
                    'alamat_sesuai_ktp' => $alamat,
                    'no_hp' => $noHp,
                    'email' => $email,
                    'kontak_darurat' => $kontakDaruratHp,
                    'hubungan_kontak_darurat' => $hubunganDarurat,
                    'nama_bank' => $bankName,
                    'no_rekening' => $noRekening,
                    'nama_rekening' => $nama,
                    'jenis_kelamin' => $jk,
                    'kode_status_kawin' => $statusKawin,
                    'pendidikan_terakhir' => $pendidikan,
                    'jurusan' => $jurusan,
                    'kode_cabang' => $kodeCabang,
                    'kode_dept' => $kodeDept,
                    'kode_jabatan' => $kodeJabatan,
                    'tanggal_masuk' => $tanggalMasuk,
                    'status_karyawan' => $statusKaryawan,
                    'foto' => null,
                    'lock_location' => '1',
                    'lock_jam_kerja' => '1',
                    'status_akses_web' => '1',
                    'status_aktif_karyawan' => $isAktif,
                    'tanggal_nonaktif' => $tanggalNonaktif,
                    'password' => $defaultPassword,
                ]
            );

            $countSeeded++;
        }

        $this->command->info("✓ Sukses! Berhasil men-seed {$countSeeded} data dummy karyawan.");
        $this->command->info("Detail Variasi:");
        $this->command->info("- Status Karyawan: Tetap (T), Kontrak (K), Probation (P), Magang (M), Freelance (F), Outsourcing (O)");
        $this->command->info("- Jenjang Pendidikan: SD, SMP, SMA, SMK, D1, D2, D3, D4, S1, S2, S3 (Lengkap beserta jurusan)");
        $this->command->info("- Cabang: TSM (Tasikmalaya), BGR (Bogor), BDG (Bandung), JKT (Jakarta), SBY (Surabaya), SMG (Semarang), YOG (Yogyakarta)");
        $this->command->info("- Departemen: ICT, AKT, SDM, MKT, PRD, LOG, GA");
        $this->command->info("- Jabatan: J01 s/d J10");
        $this->command->info("- Password Default Akun Karyawan: 12345");
    }
}
