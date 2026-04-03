<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jamkerja extends Model
{
    use HasFactory;
    protected $table = 'presensi_jamkerja';
    protected $primaryKey = 'kode_jam_kerja';
    protected $guarded = [];
    public $incrementing = false;

    /**
     * Batasi jam kerja ke jabatan tertentu (halaman pilih jam kerja).
     * Tanpa baris pivot = tidak ada yang melihat; jumlah baris = jumlah jabatan di master = dipilih semua (setara semua).
     */
    public function jabatanTerbatas(): HasMany
    {
        return $this->hasMany(PresensiJamkerjaJabatan::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }

    /** Jumlah definisi jabatan di master (untuk cek "dipilih semua"). */
    public static function hitungJabatanDefinisi(): int
    {
        return Jabatan::count();
    }

    /**
     * Jam kerja untuk seting departemen/cabang: harus ada pembatasan;
     * jika semua jabatan dipilih, semua dept; jika tidak, hanya jika ada karyawan di dept yang jabatannya terdaftar.
     */
    public function scopeVisibleUntukDepartemenCabang(Builder $query, string $kodeCabang, string $kodeDept): Builder
    {
        $totalJabatan = static::hitungJabatanDefinisi();
        if ($totalJabatan === 0) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('presensi_jamkerja_jabatan')
                    ->whereColumn('presensi_jamkerja_jabatan.kode_jam_kerja', 'presensi_jamkerja.kode_jam_kerja');
            })
            ->where(function (Builder $q) use ($kodeCabang, $kodeDept, $totalJabatan) {
                $q->whereRaw(
                    '(SELECT COUNT(DISTINCT presensi_jamkerja_jabatan.kode_jabatan) FROM presensi_jamkerja_jabatan WHERE presensi_jamkerja_jabatan.kode_jam_kerja = presensi_jamkerja.kode_jam_kerja) = ?',
                    [$totalJabatan]
                )->orWhereExists(function ($sub) use ($kodeCabang, $kodeDept) {
                    $sub->selectRaw('1')
                        ->from('presensi_jamkerja_jabatan')
                        ->join('karyawan', 'karyawan.kode_jabatan', '=', 'presensi_jamkerja_jabatan.kode_jabatan')
                        ->whereColumn('presensi_jamkerja_jabatan.kode_jam_kerja', 'presensi_jamkerja.kode_jam_kerja')
                        ->where('karyawan.kode_cabang', $kodeCabang)
                        ->where('karyawan.kode_dept', $kodeDept);
                });
            });
    }

    /**
     * Jam kerja tampil jika ada pembatasan dan (semua jabatan dipilih ATAU jabatan karyawan termasuk daftar).
     * Tanpa baris pivot = tidak tampil untuk siapa pun.
     */
    public function scopeVisibleUntukJabatanKaryawan(Builder $query, ?string $kodeJabatan): Builder
    {
        $totalJabatan = static::hitungJabatanDefinisi();
        if ($totalJabatan === 0) {
            return $query->whereRaw('1 = 0');
        }

        $query = $query->whereExists(function ($sub) {
            $sub->selectRaw('1')
                ->from('presensi_jamkerja_jabatan')
                ->whereColumn('presensi_jamkerja_jabatan.kode_jam_kerja', 'presensi_jamkerja.kode_jam_kerja');
        });

        if ($kodeJabatan === null || $kodeJabatan === '') {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($kodeJabatan, $totalJabatan) {
            $q->whereRaw(
                '(SELECT COUNT(DISTINCT presensi_jamkerja_jabatan.kode_jabatan) FROM presensi_jamkerja_jabatan WHERE presensi_jamkerja_jabatan.kode_jam_kerja = presensi_jamkerja.kode_jam_kerja) = ?',
                [$totalJabatan]
            )->orWhereExists(function ($sub) use ($kodeJabatan) {
                $sub->selectRaw('1')
                    ->from('presensi_jamkerja_jabatan')
                    ->whereColumn('presensi_jamkerja_jabatan.kode_jam_kerja', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('presensi_jamkerja_jabatan.kode_jabatan', $kodeJabatan);
            });
        });
    }
}
