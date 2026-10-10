<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinPersonelShift extends Model
{
    use HasFactory;

    protected $table = 'min_personel_shift';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_cabang',
        'kode_dept',
        'kode_grup',
        'kode_jam_kerja',
        'min_personel',
    ];

    protected $casts = [
        'min_personel' => 'integer',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function grup()
    {
        return $this->belongsTo(Grup::class, 'kode_grup', 'kode_grup');
    }

    public function jamKerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}
