<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = "karyawan";
    protected $primaryKey = "nik";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = [
        'kode_cabang_array' => 'array',
    ];

    protected function namaKaryawan(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::title(Str::lower($value))
        );
    }

    protected function namaLengkap(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatNamaLengkap()
        );
    }

    private function formatNamaLengkap()
    {
        $gelarDepan = $this->gelar_depan ? trim($this->gelar_depan) . ' ' : '';
        $gelarBelakang = $this->gelar_belakang ? ', ' . trim($this->gelar_belakang) : '';
        
        return $gelarDepan . $this->nama_karyawan . $gelarBelakang;
    }

    function getRekapstatuskaryawan($request = null)
    {
        $query = Karyawan::query();
        $query->where('status_aktif_karyawan', 1);
        $query->select(
            DB::raw("SUM(IF(status_karyawan = 'K', 1, 0)) as jml_kontrak"),
            DB::raw("SUM(IF(status_karyawan = 'T', 1, 0)) as jml_tetap"),
            DB::raw("SUM(IF(status_karyawan = 'O', 1, 0)) as jml_outsourcing"),
            DB::raw("SUM(IF(status_aktif_karyawan = '1', 1, 0)) as jml_aktif"),
        );
        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        return $query->first();
    }

    // Relasi dengan Facerecognition
    public function facerecognition()
    {
        return $this->hasMany(Facerecognition::class, 'nik', 'nik');
    }



    public function getRekapkontrak($kategori, $userCabangs = null, $userDepartemens = null)
    {
        $bulanini = date("m");
        $tahunini = date("Y");
        $start_date_bulanini = $tahunini . "-" . $bulanini . "-01";
        $end_date_bulanini = date("Y-m-t", strtotime($start_date_bulanini));
        //Jika Bulan + 1 Lebih dari 12 Maka Bulan + 1 - 12 dan Tahun + 1 Jika Tidak Maka Bulan Depan = Bulan + 1
        $bulandepan = date("m") + 1 > 12 ? (date("m") + 1) - 12 : date("m") + 1;
        $tahunbulandepan = date("m") + 1 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_bulandepan = $tahunbulandepan . "-" . $bulandepan . "-01";
        $end_date_bulandepan = date("Y-m-t", strtotime($start_date_bulandepan));

        //Jika Bulan + 2 Lebih dari 12 Maka Bulan + 2 - 12 dan Tahun + 1 Jika Tidak Maka Bulan Depan = Bulan + 2
        //Sampel Jika Bulan = Desember (12) Maka Dua bulan adalah Februari (2) (12+2-12);
        $duabulan = date("m") + 2 > 12 ? (date("m") + 2) - 12 : date("m") + 2;
        $tahunduabulan = date("m") + 2 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_duabulan = $tahunduabulan . "-" . $duabulan . "-01";
        $end_date_duabulan = date("Y-m-t", strtotime($start_date_duabulan));
        $query = Kontrak::query();
        $query->select('kontrak.no_kontrak', 'kontrak.nik', 'kontrak.sampai', 'karyawan.nama_karyawan', 'nama_jabatan', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'nama_cabang');
        $query->join('karyawan', 'kontrak.nik', '=', 'karyawan.nik');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        
        // Filter berdasarkan akses cabang dan departemen jika diberikan
        if (!empty($userCabangs) && is_array($userCabangs)) {
            $query->whereIn('karyawan.kode_cabang', $userCabangs);
        }
        
        if (!empty($userDepartemens) && is_array($userDepartemens)) {
            $query->whereIn('karyawan.kode_dept', $userDepartemens);
        }
        
        if ($kategori == 0) { // Lewat Jatuh Tempo
            $query->where('sampai', '<', $start_date_bulanini);
        } else if ($kategori == 1) { // Jatuh Tempo Bulan Ini
            $query->whereBetween('sampai', [$start_date_bulanini, $end_date_bulanini]);
        } else if ($kategori == 2) { // Jatuh Tempo Bulan Depan
            $query->whereBetween('sampai', [$start_date_bulandepan, $end_date_bulandepan]);
        } else if ($kategori == 3) { // Jatuh Tempo Dua Bulan
            $query->whereBetween('sampai', [$start_date_duabulan, $end_date_duabulan]);
        }
        $query->where('status_aktif_karyawan', 1);
        $query->where('status_karyawan', 'K');
        $query->where('status_kontrak', 1);
        $query->orderBy('kontrak.sampai');
        $query->orderBy('karyawan.nama_karyawan');
        return $query->get();
    }

    public function getRekapSip($kategori, $userCabangs = null, $userDepartemens = null)
    {
        $bulanini = date("m");
        $tahunini = date("Y");
        $start_date_bulanini = $tahunini . "-" . $bulanini . "-01";
        $end_date_bulanini = date("Y-m-t", strtotime($start_date_bulanini));

        $bulandepan = date("m") + 1 > 12 ? (date("m") + 1) - 12 : date("m") + 1;
        $tahunbulandepan = date("m") + 1 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_bulandepan = $tahunbulandepan . "-" . $bulandepan . "-01";
        $end_date_bulandepan = date("Y-m-t", strtotime($start_date_bulandepan));

        $duabulan = date("m") + 2 > 12 ? (date("m") + 2) - 12 : date("m") + 2;
        $tahunduabulan = date("m") + 2 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_duabulan = $tahunduabulan . "-" . $duabulan . "-01";
        $end_date_duabulan = date("Y-m-t", strtotime($start_date_duabulan));

        // Kategori 4: Jatuh tempo 6 bulan dari sekarang
        // Perhitungan bulan +6 otomatis melompati pergantian tahun.
        $enambulan = date("m") + 6 > 12 ? (date("m") + 6) - 12 : date("m") + 6;
        $tahunenambulan = date("m") + 6 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_enambulan = $tahunenambulan . "-" . $enambulan . "-01";
        $end_date_enambulan = date("Y-m-t", strtotime($start_date_enambulan));

        $query = Sip::query();
        $query->select('sip.no_sip', 'sip.nik', 'sip.tanggal_akhir', 'karyawan.nama_karyawan', 'nama_jabatan', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'nama_cabang');
        $query->join('karyawan', 'sip.nik', '=', 'karyawan.nik');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');

        if (!empty($userCabangs) && is_array($userCabangs)) {
            $query->whereIn('karyawan.kode_cabang', $userCabangs);
        }

        if (!empty($userDepartemens) && is_array($userDepartemens)) {
            $query->whereIn('karyawan.kode_dept', $userDepartemens);
        }

        if ($kategori == 0) {
            $query->where('sip.tanggal_akhir', '<', $start_date_bulanini);
        } elseif ($kategori == 1) {
            $query->whereBetween('sip.tanggal_akhir', [$start_date_bulanini, $end_date_bulanini]);
        } elseif ($kategori == 2) {
            $query->whereBetween('sip.tanggal_akhir', [$start_date_bulandepan, $end_date_bulandepan]);
        } elseif ($kategori == 3) {
            $query->whereBetween('sip.tanggal_akhir', [$start_date_duabulan, $end_date_duabulan]);
        } elseif ($kategori == 4) {
            $query->whereBetween('sip.tanggal_akhir', [$start_date_enambulan, $end_date_enambulan]);
        }

        $query->where('karyawan.status_aktif_karyawan', 1);
        $query->where('sip.status_sip', 1);
        $query->orderBy('sip.tanggal_akhir');
        $query->orderBy('karyawan.nama_karyawan');
        return $query->get();
    }

    // Relasi dengan GrupDetail
    // public function grupDetail()
    // {
    //     return $this->hasMany(GrupDetail::class, 'nik', 'nik');
    // }

    // Relasi ke Grup melalui GrupDetail
    // public function grup()
    // {
    //     return $this->hasManyThrough(Grup::class, GrupDetail::class, 'nik', 'kode_grup', 'nik', 'kode_grup');
    // }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan', 'kode_jabatan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }
}
