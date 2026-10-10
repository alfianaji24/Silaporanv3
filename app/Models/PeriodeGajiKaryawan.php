<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeGajiKaryawan extends Model
{
    use HasFactory;

    protected $table = 'periode_gaji_karyawan';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function periodeGaji()
    {
        return $this->belongsTo(PeriodeGaji::class, 'kode_periode_gaji', 'kode_periode_gaji');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
