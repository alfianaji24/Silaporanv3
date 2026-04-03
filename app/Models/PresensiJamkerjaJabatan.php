<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiJamkerjaJabatan extends Model
{
    protected $table = 'presensi_jamkerja_jabatan';

    protected $fillable = [
        'kode_jam_kerja',
        'kode_jabatan',
    ];

    public function jamkerja(): BelongsTo
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan', 'kode_jabatan');
    }
}
