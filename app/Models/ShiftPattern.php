<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftPattern extends Model
{
    use HasFactory;

    protected $table = 'shift_patterns';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_pola',
        'nama_pola',
        'jumlah_hari_siklus',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jumlah_hari_siklus' => 'integer',
    ];

    public function details()
    {
        return $this->hasMany(ShiftPatternDetail::class, 'kode_pola', 'kode_pola')->orderBy('urutan_hari');
    }

    public function grup()
    {
        return $this->hasMany(Grup::class, 'kode_pola', 'kode_pola');
    }
}
