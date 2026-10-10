<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeGaji extends Model
{
    use HasFactory;

    protected $table = 'periode_gaji';
    protected $primaryKey = 'kode_periode_gaji';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_dari' => 'integer',
        'tanggal_sampai' => 'integer',
        'metode_lintas_bulan' => 'integer',
    ];

    public function karyawanAssignment()
    {
        return $this->hasMany(PeriodeGajiKaryawan::class, 'kode_periode_gaji', 'kode_periode_gaji');
    }

    public function karyawanAktif()
    {
        return $this->hasMany(PeriodeGajiKaryawan::class, 'kode_periode_gaji', 'kode_periode_gaji')
            ->where('is_active', true);
    }

    public function slipGaji()
    {
        return $this->hasMany(Slipgaji::class, 'kode_periode_gaji', 'kode_periode_gaji');
    }

    public function mutasi()
    {
        return $this->hasMany(MutasiPeriodeGaji::class, 'kode_periode_baru', 'kode_periode_gaji');
    }
}
