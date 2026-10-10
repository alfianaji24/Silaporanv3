<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPeriode extends Model
{
    use HasFactory;

    protected $table = 'jadwal_periode';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_periode',
        'bulan',
        'tahun',
        'kode_cabang',
        'kode_dept',
        'kode_grup',
        'status',
        'created_by',
        'published_by',
        'published_at',
        'catatan',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'published_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(JadwalPeriodeItem::class, 'jadwal_periode_id', 'id');
    }

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
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by', 'id');
    }
}
