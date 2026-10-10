<?php

use App\Models\Detailharilibur;
use App\Models\Lembur;
use App\Models\LemburAturan;
use App\Models\Pengaturanumum;
use App\Models\Tutuplaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;

function buatkode($nomor_terakhir, $kunci, $jumlah_karakter = 0)
{
    /* mencari nomor baru dengan memecah nomor terakhir dan menambahkan 1
    string nomor baru dibawah ini harus dengan format XXX000000
    untuk penggunaan dalam format lain anda harus menyesuaikan sendiri */
    $nomor_baru = intval(substr($nomor_terakhir, strlen($kunci))) + 1;
    //    menambahkan nol didepan nomor baru sesuai panjang jumlah karakter
    $nomor_baru_plus_nol = str_pad($nomor_baru, $jumlah_karakter, "0", STR_PAD_LEFT);
    //    menyusun kunci dan nomor baru
    $kode = $kunci . $nomor_baru_plus_nol;
    return $kode;
}

function messageSuccess($message)
{
    return ['success' => $message];
}


function messageError($message)
{
    return ['error' => $message];
}


// Mengubah ke Huruf Besar
function textUpperCase($value)
{
    return strtoupper(strtolower($value));
}
// Mengubah ke CamelCase
function textCamelCase($value)
{
    return ucwords(strtolower($value));
}


function getdocMarker($file)
{
    $url = url('/storage/marker/' . $file);
    return $url;
}


function getfotoPelanggan($file)
{
    $url = url('/storage/pelanggan/' . $file . '?v=' . time());
    return $url;
}


function getfotoKaryawan($file)
{
    $url = url('/storage/karyawan/' . $file . '?v=' . time());
    return $url;
}





function toNumber($value)
{
    if (!empty($value)) {
        // Hapus semua karakter kecuali angka, koma, dan titik (untuk menangani prefix Rp dll)
        $clean = preg_replace('/[^0-9,.]/', '', $value);
        // Jika format Indonesia: titik adalah ribuan (dihapus), koma adalah desimal (ganti ke titik)
        return str_replace([".", ","], ["", "."], $clean);
    } else {
        return 0;
    }
}


function formatRupiah($nilai)
{
    return number_format($nilai, '0', ',', '.');
}

function formatAngka($nilai)
{
    if (isset($nilai) && is_numeric($nilai)) {
        return number_format($nilai, '0', ',', '.');
    }
    return $nilai;
}

function buatPesanNotifikasiAbsen(string $namaKaryawan, string $jenisAbsen, $waktu): string
{
    $waktuAbsen = Carbon::parse($waktu);
    $jam = (int) $waktuAbsen->format('G');

    if ($jam >= 5 && $jam < 11) {
        $kondisi = 'pagi';
    } elseif ($jam >= 11 && $jam < 15) {
        $kondisi = 'siang';
    } elseif ($jam >= 15 && $jam < 19) {
        $kondisi = 'sore';
    } else {
        $kondisi = 'malam';
    }

    $jenisAbsen = strtolower($jenisAbsen);
    $penutup = $jenisAbsen === 'masuk' ? 'Selamat Bekerja' : 'Selamat beristirahat';

    return "Selamat {$kondisi}, {$namaKaryawan}\n"
        . "Anda sudah melakukan absen {$jenisAbsen} " . $waktuAbsen->format('d-m-Y H:i') . "\n\n"
        . "Sudah berhasil kami terima,\n"
        . $penutup;
}


function formatAngkaDesimal($nilai)
{
    if (isset($nilai) && is_numeric($nilai)) {
        return number_format($nilai, '2', ',', '.');
    }
    return $nilai;
}



function DateToIndo($date2)
{ // fungsi atau method untuk mengubah tanggal ke format indonesia
    // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
    $BulanIndo2 = array(
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    );

    $tahun2 = substr($date2, 0, 4); // memisahkan format tahun menggunakan substring
    $bulan2 = substr($date2, 5, 2); // memisahkan format bulan menggunakan substring
    $tgl2   = substr($date2, 8, 2); // memisahkan format tanggal menggunakan substring

    $result = $tgl2 . " " . $BulanIndo2[(int)$bulan2 - 1] . " " . $tahun2;
    return ($result);
}


// function cektutupLaporan($tgl, $jenislaporan)
// {
//     $tanggal = explode("-", $tgl);
//     $bulan = $tanggal[1];
//     $tahun = $tanggal[0];
//     $cek = Tutuplaporan::where('jenis_laporan', $jenislaporan)
//         ->where('bulan', $bulan)
//         ->where('tahun', $tahun)
//         ->where('status', 1)
//         ->count();
//     return $cek;
// }


function getbulandantahunlalu($bulan, $tahun, $show)
{
    if ($bulan == 1) {
        $bulanlalu = 12;
        $tahunlalu = $tahun - 1;
    } else {
        $bulanlalu = $bulan - 1;
        $tahunlalu = $tahun;
    }

    if ($show == "tahun") {
        return $tahunlalu;
    } elseif ($show == "bulan") {
        return $bulanlalu;
    }
}


function getbulandantahunberikutnya($bulan, $tahun, $show)
{
    if ($bulan == 12) {
        $bulanberikutnya =  1;
        $tahunberikutnya = $tahun + 1;
    } else {
        $bulanberikutnya = $bulan + 1;
        $tahunberikutnya = $tahun;
    }

    if ($show == "tahun") {
        return $tahunberikutnya;
    } elseif ($show == "bulan") {
        return $bulanberikutnya;
    }
}


function lockreport($tanggal)
{
    $start_year = config('global.start_year');
    $lock_date = $start_year . "-01-01";

    if ($tanggal < $lock_date && !empty($tanggal)) {
        return "error";
    } else {
        return "success";
    }
}



// function getBeratliter($tanggal)
// {
//     if ($tanggal <= "2022-03-01") {
//         $berat = 0.9064;
//     } else {
//         $berat = 1;
//     }
//     return $berat;
// }
function formatIndo($date)
{
    $tanggal = !empty($date) ? date('d-m-Y', strtotime($date)) : '';
    return $tanggal;
}

function formatIndo2($date)
{
    $tanggal = !empty($date) ? date('d-m-y', strtotime($date)) : '';
    return $tanggal;
}

function formatIndo3($date)
{
    $tanggal = !empty($date) ? date('d-m-Y H:i', strtotime($date)) : '';
    return $tanggal;
}

function formatName2($name)
{
    // Kode ini mengambil nama lengkap dan mengembalikan hanya dua kata pertama dari nama tersebut.
    // Contoh: jika nama lengkap adalah "John Doe Smith", maka kode ini akan mengembalikan "John Doe".
    $words = explode(' ', $name); // Memecah nama menjadi array kata-kata berdasarkan spasi.
    return implode(' ', array_slice($words, 0, 2)); // Mengembalikan dua kata pertama yang dihubungkan dengan spasi.
}



function getNamaDepan($name)
{
    $words = explode(' ', $name);
    return $words[0];
}


function removeTitik($value)
{
    return str_replace('.', '', $value);
}
function getnamaHari($hari)
{
    // $hari = date("D");

    switch ($hari) {
        case 'Sun':
            $hari_ini = "Minggu";
            break;

        case 'Mon':
            $hari_ini = "Senin";
            break;

        case 'Tue':
            $hari_ini = "Selasa";
            break;

        case 'Wed':
            $hari_ini = "Rabu";
            break;

        case 'Thu':
            $hari_ini = "Kamis";
            break;

        case 'Fri':
            $hari_ini = "Jumat";
            break;

        case 'Sat':
            $hari_ini = "Sabtu";
            break;

        default:
            $hari_ini = "Tidak di ketahui";
            break;
    }

    return $hari_ini;
}


function hitungjarak($lat1, $lon1, $lat2, $lon2)
{
    $theta = $lon1 - $lon2;
    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('meters');
}


function hitungHari($startDate, $endDate)
{
    if ($startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Tambahkan 1 hari agar penghitungan inklusif
        $interval = $start->diff($end);
        $dayDifference = $interval->days + 1;

        return  $dayDifference;
    } else {
        return 0;
    }
}

function getSid($file)
{
    $url = url('/storage/uploads/sid/' . $file);
    return $url;
}

function hitungpulangcepat($tanggal_presensi, $jam_out, $jam_pulang, $istirahat, $jam_awal_istirahat, $jam_akhir_istirahat, $lintashari)
{


    $tanggal = $lintashari == 1 ? date('Y-m-d', strtotime($tanggal_presensi . ' +1 day')) : $tanggal_presensi;
    $jam_awal_istirahat = $tanggal . ' ' . $jam_awal_istirahat;
    $jam_akhir_istirahat = $tanggal . ' ' . $jam_akhir_istirahat;
    $jam_pulang = $tanggal . ' ' . $jam_pulang;

    if (empty($jam_out)) {
        return 0;
    }


    if ($istirahat == 1) {
        if ($jam_out >= $jam_akhir_istirahat) {
            $j_pulang = $jam_out;
            $pengurang = 0;
        } elseif ($jam_out < $jam_awal_istirahat) {
            $j_pulang = $jam_out;
            $pengurang = 1;
        } else {
            $j_pulang = $jam_akhir_istirahat;
            $pengurang = 0;
        }
    } else {
        $j_pulang = $jam_out;
        $pengurang = 0;
    }


    if ($j_pulang < $jam_pulang) {
        $j1 = strtotime($j_pulang);
        $j2 = strtotime($jam_pulang);
        $diffpulangcepat = $j2 - $j1;

        $jam_pulangcepat = floor($diffpulangcepat / (60 * 60));
        $menit_pulangcepat = floor(($diffpulangcepat - $jam_pulangcepat * (60 * 60)) / 60);

        $jpulangcepat = $jam_pulangcepat <= 9 ? '0' . $jam_pulangcepat : $jam_pulangcepat;
        $mpulangcepat = $menit_pulangcepat <= 9 ? '0' . $menit_pulangcepat : $menit_pulangcepat;

        $keterangan_pulangcepat = $jpulangcepat . ':' . $mpulangcepat;
        $desimal_pulangcepat = $jam_pulangcepat +   ROUND(($menit_pulangcepat / 60), 2) - $pengurang;

        return $desimal_pulangcepat;
    } else {
        return 0;
    }
}
function hitungjamterlambat($jam_in, $jam_mulai)
{

    // $jam_in = date('Y-m-d H:i', strtotime($jam_in));
    // $jam_mulai = date('Y-m-d H:i', strtotime($jam_mulai));
    if (!empty($jam_in)) {
        if ($jam_in > $jam_mulai) {
            $j1 = strtotime($jam_mulai);
            $j2 = strtotime($jam_in);

            $diffterlambat = $j2 - $j1;

            $jamterlambat = floor($diffterlambat / (60 * 60));
            $menitterlambat = floor(($diffterlambat - $jamterlambat * (60 * 60)) / 60);

            $jterlambat = $jamterlambat <= 9 ? '0' . $jamterlambat : $jamterlambat;
            $mterlambat = $menitterlambat <= 9 ? '0' . $menitterlambat : $menitterlambat;

            $keterangan_terlambat =  $jterlambat . ':' . $mterlambat;
            $desimal_terlambat = $jamterlambat +   ROUND(($menitterlambat / 60), 2);


            // if ($jamterlambat < 1 && $menitterlambat <= 5) {
            //     $color_terlambat = 'text-success';
            //     $desimal_terlambat = 0;
            // } elseif ($jamterlambat < 1 && $menitterlambat > 5) {
            //     $color_terlambat = 'text-warning';
            //     $desimal_terlambat = 0;
            // } else {
            //     $color_terlambat = 'text-danger';
            //     $desimal_terlambat = $desimal_terlambat;
            // }

            $show = $desimal_terlambat < 1 ? $menitterlambat . " Menit" : formatAngkaDesimal($desimal_terlambat) . " Jam";
            return [
                'keterangan_terlambat' => $keterangan_terlambat,
                'jamterlambat' => $jamterlambat,
                'menitterlambat' => $menitterlambat,
                'desimal_terlambat' => $desimal_terlambat,
                'show' => '<span style="color:red">' . $show . '</span>',
                'show_laporan' => 'Telat :' . $show,
                'color' => 'red'
                // 'color_terlambat' => $color_terlambat
            ];
        } else {
            return [
                'menitterlambat' => 0,
                'desimal_terlambat' => 0,
                'color' => 'green',
                'show' => '<span style="color:green">Tepat Waktu</span>',
                'show_laporan' => 'Tepat Waktu'
            ];
        }
    } else {
        return null;
    }
}


function hitungdenda($denda_list, $terlambat)
{
    static $general_setting = null;
    if ($general_setting === null) {
        $general_setting = Pengaturanumum::where('id', 1)->first();
    }
    if ($general_setting->denda == 1) {
        $denda_terlambat = 0;
        foreach ($denda_list as $denda) {
            if ($terlambat >= $denda['dari'] && $terlambat <= $denda['sampai']) {
                $denda_terlambat = $denda['denda'];
                break;
            }
        }
    } else {
        $denda_terlambat = 0;
    }
    return $denda_terlambat;
}

function hitungJumlahHari($tanggal_awal, $tanggal_akhir)
{
    $start_date = Carbon::parse($tanggal_awal);
    $end_date = Carbon::parse($tanggal_akhir);

    $jumlah_hari = $start_date->diffInDays($end_date);

    return $jumlah_hari;
}


function getdatalibur($dari, $sampai)
{
    $no = 1;
    $libur = [];
    $ceklibur = Detailharilibur::select(
        'nik',
        'tanggal',
        'kode_cabang',
        'keterangan',
    )
        ->leftJoin('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
        // ->where('kategori', 1)
        ->whereBetween('tanggal', [$dari, $sampai])->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'kode_cabang' => $d->kode_cabang,
            'tanggal' => $d->tanggal,
            'keterangan' => $d->keterangan
        ];
    }

    return $libur;
}

function ceklibur($array, $search_list)
{

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value) {

        // Iterate over each search condition
        foreach ($search_list as $k => $v) {

            // If the array element does not meet
            // the search condition then continue
            // to the next element
            if (!isset($value[$k]) || $value[$k] != $v) {

                // Skip two loops
                continue 2;
            }
        }

        // Append array element's key to the
        //result array
        $result[] = $value;
    }

    // Return result
    return $result;
}

function getHari($date)
{
    $days = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    $dayName = date('l', strtotime($date));
    return $days[$dayName];
}


function getNamabulan($bulan)
{
    $namabulan = array(
        '1' => 'Januari',
        '2' => 'Februari',
        '3' => 'Maret',
        '4' => 'April',
        '5' => 'Mei',
        '6' => 'Juni',
        '7' => 'Juli',
        '8' => 'Agustus',
        '9' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    );
    return $namabulan[$bulan];
}

function hitungJam($startDate, $endDate)
{
    if ($startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Tambahkan 1 detik agar penghitungan inklusif
        $timeDifference = $end->getTimestamp() - $start->getTimestamp() + 1;
        $hourDifference = $timeDifference / 3600;

        return $hourDifference;
    } else {
        return 0;
    }
}


function getlembur($dari, $sampai)
{
    $no = 1;
    $lembur = [];
    $ceklembur = Lembur::select(
        'nik',
        'tanggal',
        'lembur_mulai',
        'lembur_selesai',
        'lembur_in',
        'lembur_out',
    )
        ->whereBetween('tanggal', [$dari, $sampai])
        ->get();


    foreach ($ceklembur as $d) {
        $lembur[] = [
            'nik' => $d->nik,
            'tanggal' => $d->tanggal,
            'lembur_mulai' => $d->lembur_mulai,
            'lembur_selesai' => $d->lembur_selesai,
            'lembur_in' => $d->lembur_in,
            'lembur_out' => $d->lembur_out,
        ];
    }

    return $lembur;
}



function ceklembur($array, $search_list)
{

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value) {

        // Iterate over each search condition
        foreach ($search_list as $k => $v) {

            // If the array element does not meet
            // the search condition then continue
            // to the next element
            if (!isset($value[$k]) || $value[$k] != $v) {

                // Skip two loops
                continue 2;
            }
        }

        // Append array element's key to the
        //result array
        $result[] = $value;
    }

    // Return result
    return $result;
}

function hitungjamlembur($jam1, $jam2)
{
    $j1 = strtotime($jam1);
    $j2 = strtotime($jam2);

    $diffterlambat = $j2 - $j1;

    $jamterlambat = floor($diffterlambat / (60 * 60));
    $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60))) / 60);

    $desimalterlambat = $jamterlambat + ROUND(($menitterlambat / 60), 2);

    return $desimalterlambat;
}

function hitungLembur($datalembur)
{
    if (!empty($datalembur) && $datalembur[0]['lembur_in'] && $datalembur[0]['lembur_out']) {
        $lembur_mulai = $datalembur[0]['lembur_mulai'];
        $lembur_selesai = $datalembur[0]['lembur_selesai'];
        $lembur_in = $datalembur[0]['lembur_in'];
        $lembur_out = $datalembur[0]['lembur_out'];


        $start_lembur = $lembur_in < $lembur_mulai ? $lembur_mulai  : $lembur_in;
        $end_lembur = $lembur_out > $lembur_selesai ? $lembur_selesai : $lembur_out;

        $jam_lembur = hitungjamlembur($start_lembur, $end_lembur);
        return $jam_lembur;
    } else {
        return 0;
    }
}
function hitungSisaHari($endDate)
{
    $today = new DateTime(date('Y-m-d'));
    $end = new DateTime($endDate);
    $end->setTime(0, 0, 0);

    $interval = $today->diff($end);
    $daysRemaining = $interval->days;

    if ($today > $end) {
        $daysRemaining = -$daysRemaining;
    }

    return $daysRemaining;
}

function formatName($fullName)
{
    // Pisahkan string menjadi array kata-kata
    $words = explode(' ', $fullName);

    // Jika ada lebih dari 3 kata
    if (count($words) >= 3) {
        // Ambil dua kata pertama
        $firstTwoWords = array_slice($words, 0, 2);

        // Ambil huruf pertama dari setiap kata setelah dua kata pertama
        $initials = array_map(function ($word) {
            return strtoupper($word[0]);
        }, array_slice($words, 2));

        // Gabungkan dua kata pertama dengan inisial-inisial
        $formattedName = implode(' ', $firstTwoWords) . ' ' . implode('', $initials);
    } else {
        // Jika tidak lebih dari 3 kata, gunakan nama asli
        $formattedName = $fullName;
    }

    return $formattedName;
}

function singkatString($string)
{
    $words = explode(' ', $string);

    // Jika string terdiri dari tepat 3 kata, buat singkatan huruf besar
    if (count($words) === 3) {
        $abbreviation = '';

        foreach ($words as $word) {
            if (strlen($word) >= 3) {
                $abbreviation .= strtoupper($word[0]);
            }
        }

        return $abbreviation;
    }

    // Jika tidak, buat camelCase
    return ucwords(strtolower($string));
}

/**
 * Check if a date is a holiday or off-day for a specific employee.
 * Logic uses 4-level schedule priority.
 */
function isLiburKaryawan($nik, $tanggal)
{
    // 1. Cek Libur Nasional (Hari Libur Umum)
    $ceklibur_nasional = DB::table('hari_libur_detail')
        ->join('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
        ->where('tanggal', $tanggal)
        ->first();

    if ($ceklibur_nasional) {
        return true;
    }

    // 2. Cek Jadwal Karyawan (Prioritas 4 Level)
    $hari_en = date('D', strtotime($tanggal)); // Day name in English (3 letters)
    $hari = getnamaHari($hari_en); // Day name in Indonesian (e.g., Jumat)

    // Level 1: presensi_jamkerja_bydate
    $jk_bydate = DB::table('presensi_jamkerja_bydate')->where('nik', $nik)->where('tanggal', $tanggal)->first();
    if ($jk_bydate) return false;

    // Level 2: grup_jamkerja_bydate
    $jk_grup = DB::table('grup_detail')
        ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
        ->where('grup_detail.nik', $nik)
        ->where('grup_jamkerja_bydate.tanggal', $tanggal)
        ->first();
    if ($jk_grup) return false;

    // Level 3: presensi_jamkerja_byday
    $jk_byday = DB::table('presensi_jamkerja_byday')->where('nik', $nik)->where('hari', $hari)->first();
    if ($jk_byday) return false;

    // Level 4: presensi_jamkerja_bydept_detail (Requires dept & cabang)
    $karyawan = DB::table('karyawan')->where('nik', $nik)->select('kode_dept', 'kode_cabang')->first();
    if ($karyawan) {
        $jk_bydept = DB::table('presensi_jamkerja_bydept')
            ->join('presensi_jamkerja_bydept_detail', 'presensi_jamkerja_bydept.kode_jk_dept', '=', 'presensi_jamkerja_bydept_detail.kode_jk_dept')
            ->where('kode_dept', $karyawan->kode_dept)
            ->where('kode_cabang', $karyawan->kode_cabang)
            ->where('hari', $hari)
            ->first();
        if ($jk_bydept) return false;
    }

    // Jika tidak ada jadwal sama sekali, maka itu hari LIBUR (Off day)
    return true;
}

/**
 * Calculate "Jam Netto" based on tiered multipliers from lembur_aturan.
 */
function hitungJamNetto($jam_aktual, $tipe_hari)
{
    // Static cache: fetch rules only once per tipe_hari per request
    static $rules_cache = [];
    if (!isset($rules_cache[$tipe_hari])) {
        $rules_cache[$tipe_hari] = LemburAturan::where('tipe_hari', $tipe_hari)
            ->orderBy('jam_dari', 'asc')
            ->get();
    }
    $rules = $rules_cache[$tipe_hari];

    $jam_netto = 0;
    $sisa_jam = $jam_aktual;

    foreach ($rules as $rule) {
        $start = $rule->jam_dari; // Direct use of 0-based start from DB
        $end = $rule->jam_sampai ?: 999;
        
        // Calculate the portion of overtime that falls within this absolute tier [start, end]
        $jam_di_tier_ini = max(0, min($jam_aktual, $end) - $start);
        
        if ($jam_di_tier_ini > 0) {
            $jam_netto += ($jam_di_tier_ini * $rule->faktor);
        }
    }

    return round($jam_netto, 2);
}

/**
 * Calculate excess break time deduction.
 * Comparison between actual break duration and scheduled duration.
 * @param string $start_break Time when employee goes out for break (e.g., istirahat_out)
 * @param string $end_break Time when employee comes back from break (e.g., istirahat_in)
 */
function hitungPotonganIstirahat($start_break, $end_break, $jam_awal_istirahat, $jam_akhir_istirahat)
{
    if (!empty($start_break) && !empty($end_break)) {
        $awal = strtotime($start_break);
        $akhir = strtotime($end_break);
        $durasi_riil = $akhir - $awal;

        // Use the date from start_break to build the scheduled timestamps
        $tgl = date('Y-m-d', $awal);
        $awal_skd = strtotime($tgl . ' ' . $jam_awal_istirahat);
        $akhir_skd = strtotime($tgl . ' ' . $jam_akhir_istirahat);
        
        // Handle if scheduled break ends on next day (rare but possible)
        if ($akhir_skd < $awal_skd) {
             $akhir_skd = strtotime($tgl . ' ' . $jam_akhir_istirahat . ' +1 day');
        }

        $durasi_skd = $akhir_skd - $awal_skd;

        if ($durasi_riil > $durasi_skd) {
            $selisih = $durasi_riil - $durasi_skd;
            $jam = floor($selisih / 3600);
            $menit = floor(($selisih % 3600) / 60);
            $desimal = $jam + round($menit / 60, 2);
            return $desimal;
        }
    }
    return 0;
}


/**
 * Get active special overtime rate for an employee.
 */
function getLemburKhusus($nik)
{
    return \App\Models\LemburKaryawanKhusus::where('nik', $nik)->where('status', 1)->first();
}

/**
 * Hitung rentang tanggal (dari & sampai) berdasarkan konfigurasi periode gaji.
 * 
 * @param int $bulan Bulan (1-12)
 * @param int $tahun Tahun (misal 2026)
 * @param \App\Models\PeriodeGaji|null $periode Model PeriodeGaji (opsional)
 * @return array ['dari' => 'YYYY-MM-DD', 'sampai' => 'YYYY-MM-DD', 'nama_periode' => '...']
 */
function getRentangPeriodeGaji($bulan, $tahun, $periode = null)
{
    if ($periode) {
        $tgl_dari = $periode->tanggal_dari;
        $tgl_sampai = $periode->tanggal_sampai;
        $metode = $periode->metode_lintas_bulan; // 0=sama, 1=lalu-ini, 2=ini-depan
    } else {
        $setting = Pengaturanumum::where('id', 1)->first();
        $tgl_dari = $setting->periode_laporan_dari ?? 1;
        $tgl_sampai = $setting->periode_laporan_sampai ?? 31;
        $metode = $setting->periode_laporan_next_bulan ?? 0;
    }

    if ($metode == 1) {
        // Bulan lalu ke bulan ini
        if ($bulan == 1) {
            $bulan_dari = 12;
            $tahun_dari = $tahun - 1;
        } else {
            $bulan_dari = $bulan - 1;
            $tahun_dari = $tahun;
        }
        $bulan_sampai = $bulan;
        $tahun_sampai = $tahun;
    } elseif ($metode == 2) {
        // Bulan ini ke bulan depan
        $bulan_dari = $bulan;
        $tahun_dari = $tahun;
        if ($bulan == 12) {
            $bulan_sampai = 1;
            $tahun_sampai = $tahun + 1;
        } else {
            $bulan_sampai = $bulan + 1;
            $tahun_sampai = $tahun;
        }
    } else {
        // Satu bulan yang sama
        $bulan_dari = $bulan;
        $tahun_dari = $tahun;
        $bulan_sampai = $bulan;
        $tahun_sampai = $tahun;
    }

    $bulan_dari = str_pad($bulan_dari, 2, '0', STR_PAD_LEFT);
    $last_day_start = date('t', strtotime($tahun_dari . '-' . $bulan_dari . '-01'));
    $p_dari_val = min($tgl_dari, $last_day_start);
    $p_dari = str_pad($p_dari_val, 2, '0', STR_PAD_LEFT);

    $bulan_sampai = str_pad($bulan_sampai, 2, '0', STR_PAD_LEFT);
    $last_day_end = date('t', strtotime($tahun_sampai . '-' . $bulan_sampai . '-01'));
    $p_sampai_val = min($tgl_sampai, $last_day_end);
    $p_sampai = str_pad($p_sampai_val, 2, '0', STR_PAD_LEFT);

    return [
        'dari' => $tahun_dari . '-' . $bulan_dari . '-' . $p_dari,
        'sampai' => $tahun_sampai . '-' . $bulan_sampai . '-' . $p_sampai,
        'nama_periode' => $periode->nama_periode ?? 'Default (Global)',
        'kode_periode' => $periode->kode_periode_gaji ?? null,
    ];
}

/**
 * Dapatkan periode gaji aktif seorang karyawan pada bulan/tahun tertentu.
 * 
 * @param string $nik
 * @param int|null $bulan
 * @param int|null $tahun
 * @return \App\Models\PeriodeGaji|null
 */
function getPeriodeGajiKaryawanByDate($nik, $bulan = null, $tahun = null)
{
    // Jika bulan dan tahun disertakan, cek riwayat mutasi / assignment historis
    if ($bulan !== null && $tahun !== null) {
        $targetDate = sprintf('%04d-%02d-01', $tahun, $bulan);

        $assignment = \App\Models\PeriodeGajiKaryawan::with('periodeGaji')
            ->where('nik', $nik)
            ->first();

        // 1. Cek apakah ada mutasi setelah tanggal target (yang berarti saat tanggal target karyawan ada di periode lama)
        $mutasiSetelahTarget = \App\Models\MutasiPeriodeGaji::with('periodeLama')
            ->where('nik', $nik)
            ->where('tanggal_efektif', '>', $targetDate)
            ->orderBy('tanggal_efektif', 'asc')
            ->first();

        if ($mutasiSetelahTarget) {
            // Pada bulan/tahun tersebut, karyawan berada di periode lama
            return $mutasiSetelahTarget->periodeLama;
        }

        // 2. Cek apakah ada mutasi pada atau sebelum tanggal target
        $mutasiSebelumTarget = \App\Models\MutasiPeriodeGaji::with('periodeBaru')
            ->where('nik', $nik)
            ->where('tanggal_efektif', '<=', $targetDate)
            ->orderBy('tanggal_efektif', 'desc')
            ->first();

        if ($mutasiSebelumTarget) {
            return $mutasiSebelumTarget->periodeBaru;
        }

        return $assignment ? $assignment->periodeGaji : null;
    }

    // Default: Ambil assignment aktif saat ini
    $assignment = \App\Models\PeriodeGajiKaryawan::with('periodeGaji')
        ->where('nik', $nik)
        ->where('is_active', true)
        ->first();

    return $assignment ? $assignment->periodeGaji : null;
}

/**
 * Dapatkan jam kerja karyawan pada tanggal tertentu berdasarkan hierarki prioritas
 * 
 * @param string $nik
 * @param string $tanggal (Format: Y-m-d)
 * @return \App\Models\Jamkerja|object|null
 */
function getJamKerjaKaryawanByDate($nik, $tanggal)
{
    $karyawan = \App\Models\Karyawan::where('nik', $nik)->first();
    if (!$karyawan) {
        return null;
    }

    $namahari = getnamaHari(date('D', strtotime($tanggal)));
    $kode_dept = $karyawan->kode_dept;

    // 1. Cek Ajuan Jadwal yang sudah disetujui (Approved)
    $ajuan_jadwal = \App\Models\AjuanJadwal::where('nik', $nik)
        ->where('tanggal', $tanggal)
        ->where('status', 'a')
        ->first();

    if ($ajuan_jadwal) {
        return \App\Models\Jamkerja::where('kode_jam_kerja', $ajuan_jadwal->kode_jam_kerja_tujuan)->first();
    }

    // 2. Cek Jam Kerja By Date (Khusus karyawan per tanggal)
    $jamkerja = \App\Models\Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
        ->where('nik', $nik)
        ->where('tanggal', $tanggal)
        ->first();

    if ($jamkerja) {
        return $jamkerja;
    }

    // 3. Cek Jam Kerja Grup By Date
    $cek_group = \App\Models\GrupDetail::where('nik', $nik)->first();
    if ($cek_group) {
        $jamkerja = \App\Models\GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
            ->where('tanggal', $tanggal)
            ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->first();

        if ($jamkerja) {
            return $jamkerja;
        }
    }

    // 4. Cek Jam Kerja Harian (By Day per karyawan)
    $jamkerja = \App\Models\Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
        ->where('nik', $nik)
        ->where('hari', $namahari)
        ->first();

    if ($jamkerja) {
        return $jamkerja;
    }

    // 5. Cek Jam Kerja By Dept
    $jamkerja = \App\Models\Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
        ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
        ->where('kode_dept', $kode_dept)
        ->where('kode_cabang', $karyawan->kode_cabang)
        ->where('hari', $namahari)
        ->first();

    if ($jamkerja) {
        return $jamkerja;
    }

    // 6. Fallback: Jam Kerja Global
    $general_setting = \App\Models\Pengaturanumum::where('id', 1)->first();
    if ($general_setting && $general_setting->global_jamkerja_aktif) {
        $globalJk = \App\Models\GlobalJamkerja::where('hari', $namahari)->first();
        if ($globalJk && $globalJk->kode_jam_kerja) {
            return \App\Models\Jamkerja::where('kode_jam_kerja', $globalJk->kode_jam_kerja)->first();
        }
    }

    return null;
}

if (!function_exists('getJamKerja')) {
    /**
     * Alias getJamKerja untuk kompatibilitas PRD & Roster Board
     */
    function getJamKerja($nik, $tanggal)
    {
        return getJamKerjaKaryawanByDate($nik, $tanggal);
    }
}

/**
 * Cek apakah tanggal tertentu merupakan hari libur untuk karyawan
 * 
 * @param string $nik
 * @param string $tanggal (Format: Y-m-d)
 * @return \App\Models\Detailharilibur|object|null
 */
function cekHariLiburKaryawan($nik, $tanggal)
{
    return \App\Models\Detailharilibur::join('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
        ->where('nik', $nik)
        ->where('tanggal', $tanggal)
        ->first();
}

/**
 * Hitung hari cuti efektif (hanya hari kerja, abaikan hari libur dan off)
 * 
 * @param string $nik
 * @param string $dari (Format: Y-m-d)
 * @param string $sampai (Format: Y-m-d)
 * @return array ['total_hari' => int, 'detail_hari' => array, 'hari_libur' => array, 'hari_off' => array]
 */
function hitungHariCuti($nik, $dari, $sampai)
{
    if (empty($nik) || empty($dari) || empty($sampai)) {
        return [
            'total_hari' => 0,
            'detail_hari' => [],
            'hari_libur' => [],
            'hari_off' => [],
        ];
    }

    $start = strtotime($dari);
    $end = strtotime($sampai);

    if ($start > $end) {
        return [
            'total_hari' => 0,
            'detail_hari' => [],
            'hari_libur' => [],
            'hari_off' => [],
        ];
    }

    $detail_hari = [];
    $hari_libur = [];
    $hari_off = [];

    $current = $start;
    while ($current <= $end) {
        $tgl = date('Y-m-d', $current);

        // 1. Cek Hari Libur Nasional / Perusahaan
        $libur = cekHariLiburKaryawan($nik, $tgl);
        if ($libur) {
            $hari_libur[] = [
                'tanggal' => $tgl,
                'keterangan' => $libur->keterangan ?? 'Hari Libur',
            ];
            $current = strtotime('+1 day', $current);
            continue;
        }

        // 2. Cek Jadwal / Jam Kerja
        $jamkerja = getJamKerjaKaryawanByDate($nik, $tgl);
        if (!$jamkerja) {
            $hari_off[] = [
                'tanggal' => $tgl,
                'keterangan' => 'Tidak Ada Jadwal / Hari Libur Shift',
            ];
            $current = strtotime('+1 day', $current);
            continue;
        }

        // 3. Hari Kerja Valid
        $detail_hari[] = [
            'tanggal' => $tgl,
            'jamkerja' => $jamkerja,
        ];

        $current = strtotime('+1 day', $current);
    }

    return [
        'total_hari' => count($detail_hari),
        'detail_hari' => $detail_hari,
        'hari_libur' => $hari_libur,
        'hari_off' => $hari_off,
    ];
}

/**
 * Hitung hari kerja efektif untuk seluruh jenis izin (absen, sakit, cuti, dinas)
 */
function hitungHariKerja($nik, $dari, $sampai)
{
    return hitungHariCuti($nik, $dari, $sampai);
}

/**
 * Get active admin theme ('v2' or 'classic')
 */
function get_admin_theme()
{
    static $theme = null;
    if ($theme === null) {
        $setting = \App\Models\Pengaturanumum::where('id', 1)->first();
        $theme = $setting->admin_theme ?? 'v2';
    }
    return $theme;
}


