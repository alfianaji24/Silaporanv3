<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPeriodeItem extends Model
{
    use HasFactory;

    protected $table = 'jadwal_periode_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jadwal_periode_id',
        'nik',
        'tanggal',
        'kode_jam_kerja',
        'sumber',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    public function periode()
    {
        return $this->belongsTo(JadwalPeriode::class, 'jadwal_periode_id', 'id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function jamKerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}
