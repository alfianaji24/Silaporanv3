<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BirthdayController extends Controller
{
    /**
     * Display public birthday list with pagination
     */
    public function index()
    {
        // Ambil semua karyawan aktif dan ASN (non aktif) dengan data lengkap
        $karyawanQuery = Karyawan::where(function($query) {
                $query->where('status_aktif_karyawan', 1)
                      ->orWhere('status_karyawan', 'A'); // Include ASN even if inactive
            })
            ->whereNotNull('tanggal_lahir')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('status_karyawan', 'karyawan.status_karyawan', '=', 'status_karyawan.kode_status_karyawan')
            ->select(
                'karyawan.nik',
                'karyawan.nama_karyawan',
                'karyawan.tanggal_lahir',
                'karyawan.status_aktif_karyawan',
                'karyawan.status_karyawan',
                'status_karyawan.nama_status_karyawan',
                'jabatan.nama_jabatan',
                'departemen.nama_dept',
                'cabang.nama_cabang'
            );

        // Hitung umur dan hari sampai ulang tahun untuk setiap karyawan
        $karyawanCollection = $karyawanQuery->get()->transform(function ($item) {
            $today = Carbon::now(config('app.timezone'));
            $birthday = Carbon::parse($item->tanggal_lahir);
            
            // Hitung umur saat ini
            $item->umur = $birthday->age;
            
            // Hitung ulang tahun tahun ini
            $birthdayThisYear = $birthday->copy()->year($today->year);
            
            // Check if today is birthday
            $isTodayBirthday = $birthdayThisYear->isSameDay($today);
            
            // Jika hari ini adalah ulang tahun, daysUntil = 0
            if ($isTodayBirthday) {
                $daysUntilBirthday = 0;
                $item->tanggal_ultah_selanjutnya = $birthdayThisYear->format('d-m-Y');
            } else {
                // Jika ulang tahun tahun ini sudah lewat, hitung untuk tahun depan
                if ($birthdayThisYear->lt($today)) {
                    $birthdayNextYear = $birthday->copy()->year($today->year + 1);
                    $daysUntilBirthday = $today->diffInDays($birthdayNextYear);
                    $item->tanggal_ultah_selanjutnya = $birthdayNextYear->format('d-m-Y');
                } else {
                    // Jika ulang tahun tahun ini belum lewat
                    // Gunakan startOfDay untuk memastikan perhitungan hari yang benar
                    $todayStart = $today->copy()->startOfDay();
                    $birthdayStart = $birthdayThisYear->copy()->startOfDay();
                    $daysUntilBirthday = $todayStart->diffInDays($birthdayStart);
                    $item->tanggal_ultah_selanjutnya = $birthdayThisYear->format('d-m-Y');
                }
            }
            
            $item->hari_sampai_ultah = $daysUntilBirthday;
            $item->hari_ultah = $birthday->format('d-m');
            
            return $item;
        });

        // Urutkan berdasarkan hari sampai ulang tahun (yang paling dekat dulu)
        $sortedKaryawan = $karyawanCollection->sortBy('hari_sampai_ultah')->values();

        // Buat pagination manual dengan 20 items per page tanpa info text
        $currentPage = request()->get('page', 1);
        $perPage = 20;
        $total = $sortedKaryawan->count();
        $items = $sortedKaryawan->forPage($currentPage, $perPage);

        // Create LengthAwarePaginator
        $karyawan = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return view('public.birthday', compact('karyawan'));
    }
}
