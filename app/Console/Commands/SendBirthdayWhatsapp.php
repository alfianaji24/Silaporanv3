<?php

namespace App\Console\Commands;

use App\Jobs\SendWaMessage;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBirthdayWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:birthday-whatsapp {--kode_cabang=} {--kode_dept=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim automatik ucapan ulang tahun karyawan via WhatsApp pada jam 08:00.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::now(config('app.timezone'));

        $query = Karyawan::where('status_aktif_karyawan', 1)
            ->whereMonth('tanggal_lahir', $today->month)
            ->whereDay('tanggal_lahir', $today->day)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '');

        if ($this->option('kode_cabang')) {
            $query->where('kode_cabang', $this->option('kode_cabang'));
        }

        if ($this->option('kode_dept')) {
            $query->where('kode_dept', $this->option('kode_dept'));
        }

        $birthdayPeople = $query->get();

        if ($birthdayPeople->count() === 0) {
            $this->info('Tidak ada karyawan ulang tahun hari ini.');
            return 0;
        }

        $count = 0;

        foreach ($birthdayPeople as $karyawan) {
            try {
                $umur = Carbon::parse($karyawan->tanggal_lahir)->age;

                $message = "🎉 *Selamat Ulang Tahun!* 🎂\n\n";
                $message .= "Halo *{$karyawan->nama_karyawan}*,\n\n";
                $message .= "Di hari yang istimewa ini, kami ingin mengucapkan:\n\n";
                $message .= "🎂 *Selamat Ulang Tahun yang ke-{$umur}!* 🎂\n\n";
                $message .= "Semoga di hari ulang tahunmu ini:\n";
                $message .= "✨ Panjang umur\n";
                $message .= "✨ Sehat selalu\n";
                $message .= "✨ Bahagia selalu\n";
                $message .= "✨ Sukses dalam karir\n";
                $message .= "✨ Diberkahi rezeki yang berlimpah\n\n";
                $message .= "Terima kasih atas dedikasi dan kontribusinya selama ini. Semoga hubungan kerja kita terus berjalan dengan baik!\n\n";
                $message .= "*Salam Hangat,*\nTim HR";

                $phoneNumber = preg_replace('/^0+/', '', trim($karyawan->no_hp));
                if (!str_starts_with($phoneNumber, '62')) {
                    $phoneNumber = '62' . $phoneNumber;
                }

                SendWaMessage::dispatch($phoneNumber, $message, true);
                $count++;
            } catch (\Exception $e) {
                $this->error("Gagal mengirim ke {$karyawan->nama_karyawan} ({$karyawan->no_hp}): " . $e->getMessage());
            }
        }

        $this->info("Ucapan ulang tahun dipicu untuk {$count} karyawan.");

        return 0;
    }
}
