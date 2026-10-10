<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaCicilan extends Model
{
    use HasFactory;

    protected $table = 'rencana_cicilan';
    protected $guarded = ['id'];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id', 'id');
    }

    public function pembayaran_pinjaman()
    {
        return $this->hasMany(PembayaranPinjaman::class, 'rencana_cicilan_id', 'id');
    }
}
