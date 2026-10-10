<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalCard extends Model
{
    use HasFactory;

    protected $table = 'jadwal_cards';
    protected $guarded = [];

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
