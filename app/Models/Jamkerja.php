<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jamkerja extends Model
{
    use HasFactory;
    protected $table = 'presensi_jamkerja';
    protected $primaryKey = 'kode_jam_kerja';
    protected $guarded = [];
    public $incrementing = false;

    public function jabatans()
    {
        return $this->belongsToMany(
            Jabatan::class,
            'presensi_jam_kerja',
            'kode_jam_kerja',
            'kode_jabatan'
        );
    }

    public function scopeAvailableForJabatan($query, string $kodeJabatan)
    {
        return $query->whereHas('jabatans', function ($query) use ($kodeJabatan) {
            $query->where('jabatan.kode_jabatan', $kodeJabatan);
        });
    }

    public function isAvailableForJabatan(string $kodeJabatan): bool
    {
        return $this->jabatans()
            ->where('jabatan.kode_jabatan', $kodeJabatan)
            ->exists();
    }
}
