<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiPeriodeGaji extends Model
{
    use HasFactory;

    protected $table = 'mutasi_periode_gaji';
    protected $guarded = [];

    protected $casts = [
        'tanggal_efektif' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function periodeLama()
    {
        return $this->belongsTo(PeriodeGaji::class, 'kode_periode_lama', 'kode_periode_gaji');
    }

    public function periodeBaru()
    {
        return $this->belongsTo(PeriodeGaji::class, 'kode_periode_baru', 'kode_periode_gaji');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
