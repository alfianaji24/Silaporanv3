<?php

namespace App\Console\Commands;

use App\Jobs\SendWaMessage;
use App\Models\Cabang;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendCheckoutReminderWhatsapp extends Command
{
    protected $signature = 'send:checkout-reminder-whatsapp';

    protected $description = 'Kirim pengingat WA ke karyawan yang belum absen pulang setelah jam pulang jadwal (maks. +2 jam).';

    public function handle(): int
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();

        if (!$generalsetting || $generalsetting->notifikasi_wa != 1) {
            $this->info('Notifikasi WA nonaktif, pengingat absen pulang dilewati.');
            return 0;
        }

        $defaultTimezone = $generalsetting->timezone ?? config('app.timezone');
        $nowDefault = Carbon::now($defaultTimezone);
        $today = $nowDefault->format('Y-m-d');
        $yesterday = $nowDefault->copy()->subDay()->format('Y-m-d');

        $presensiList = Presensi::whereNotNull('jam_in')
            ->whereNull('jam_out')
            ->whereIn('tanggal', [$today, $yesterday])
            ->get();

        if ($presensiList->isEmpty()) {
            $this->info('Tidak ada karyawan yang belum absen pulang.');
            return 0;
        }

        $cabangCache = [];
        $sentCount = 0;

        foreach ($presensiList as $presensi) {
            try {
                $karyawan = Karyawan::where('nik', $presensi->nik)
                    ->where('status_aktif_karyawan', 1)
                    ->whereNotNull('no_hp')
                    ->where('no_hp', '!=', '')
                    ->first();

                if (!$karyawan) {
                    continue;
                }

                $jamKerja = Jamkerja::where('kode_jam_kerja', $presensi->kode_jam_kerja)->first();

                if (!$jamKerja || Jamkerja::isKodeLibur($jamKerja->kode_jam_kerja)) {
                    continue;
                }

                $lintashari = (int) ($jamKerja->lintashari ?? 0);

                if ($presensi->tanggal === $yesterday && $lintashari !== 1) {
                    continue;
                }

                $kodeCabang = $karyawan->kode_cabang;
                if (!isset($cabangCache[$kodeCabang])) {
                    $cabang = Cabang::where('kode_cabang', $kodeCabang)->first();
                    $cabangCache[$kodeCabang] = $cabang->timezone ?? $defaultTimezone;
                }

                $timezone = $cabangCache[$kodeCabang];
                $now = Carbon::now($timezone);

                $tanggalPulang = $lintashari === 1
                    ? Carbon::parse($presensi->tanggal, $timezone)->addDay()->format('Y-m-d')
                    : $presensi->tanggal;

                $jamPulangCarbon = Carbon::parse("{$tanggalPulang} {$jamKerja->jam_pulang}", $timezone);
                $mulaiPengingat = $jamPulangCarbon->copy()->addHour();
                $batasPengingat = $jamPulangCarbon->copy()->addHours(2);

                if ($now->lt($mulaiPengingat) || $now->gt($batasPengingat)) {
                    continue;
                }

                $cacheKey = "pengingat_absen_pulang:{$presensi->nik}:{$presensi->tanggal}";
                if (Cache::has($cacheKey)) {
                    continue;
                }

                $waktuSekarang = $now->format('Y-m-d H:i');

                $message = "📢 PENGINGAT ABSEN PULANG 🚨\n\n"
                    . "Halo {$karyawan->nama_karyawan}, Waktu kerja telah selesai, namun Anda belum tercatat melakukan absensi pulang.\n\n"
                    . "🕒 Waktu Sekarang: {$waktuSekarang}\n"
                    . "📝 Silahkan absen pulang melalui Aplikasi Mobile / Mesin Fingerprint\n\n"
                    . "Mohon pastikan Anda melakukan absensi pulang sebelum meninggalkan Puskesmas guna menjaga keakuratan rekapitulasi jam kerja. Bagi yang masih membereskan pekerjaan, harap segera melakukan absensi begitu seluruh tugas selesai.\n\n"
                    . "_This is an automatically generated notification, please do not reply to this message._";
                SendWaMessage::dispatch($karyawan->no_hp, $message);

                $ttlSeconds = max(60, (int) $batasPengingat->diffInSeconds($now));
                Cache::put($cacheKey, true, $ttlSeconds);

                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Gagal mengirim pengingat absen pulang', [
                    'nik' => $presensi->nik ?? null,
                    'tanggal' => $presensi->tanggal ?? null,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Gagal proses presensi {$presensi->nik}: {$e->getMessage()}");
            }
        }

        $this->info("Pengingat absen pulang dipicu untuk {$sentCount} karyawan.");

        return 0;
    }
}
