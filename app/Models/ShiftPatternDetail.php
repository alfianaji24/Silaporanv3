<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftPatternDetail extends Model
{
    use HasFactory;

    protected $table = 'shift_pattern_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_pola',
        'urutan_hari',
        'kode_jam_kerja',
    ];

    protected $casts = [
        'urutan_hari' => 'integer',
    ];

    public function pattern()
    {
        return $this->belongsTo(ShiftPattern::class, 'kode_pola', 'kode_pola');
    }

    public function jamKerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}
