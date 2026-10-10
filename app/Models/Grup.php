<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{
    use HasFactory;

    protected $table = 'grup';
    protected $primaryKey = 'kode_grup';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_grup',
        'nama_grup',
        'kode_pola',
        'tanggal_mulai_pola'
    ];

    // Relasi ke ShiftPattern
    public function shiftPattern()
    {
        return $this->belongsTo(ShiftPattern::class, 'kode_pola', 'kode_pola');
    }

    // Relasi ke GrupDetail
    public function grupDetail()
    {
        return $this->hasMany(GrupDetail::class, 'kode_grup', 'kode_grup');
    }

    // Relasi ke Karyawan melalui GrupDetail
    public function karyawan()
    {
        return $this->hasManyThrough(Karyawan::class, GrupDetail::class, 'kode_grup', 'nik', 'kode_grup', 'nik');
    }
}
