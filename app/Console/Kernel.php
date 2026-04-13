<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckFailedMessagesJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Dipicu oleh cron: * * * * * php artisan schedule:run
        // Tidak perlu cron terpisah untuk queue:work selama baris ini tetap ada.
        $schedule->command(
            'queue:work --queue=default --sleep=3 --tries=3 --max-time=3600 --stop-when-empty'
        )
            ->everyMinute()
            ->withoutOverlapping(15)
            ->appendOutputTo(storage_path('logs/queue-scheduler.log'));

        // Kirim ucapan ulang tahun otomatis setiap hari jam 08:00
        $schedule->command('send:birthday-whatsapp')->dailyAt('08:00')->appendOutputTo(storage_path('logs/birthday-scheduler.log'));

        // Cek pesan gagal terkirim setiap 1 jam
        $schedule->job(new CheckFailedMessagesJob)
            ->hourly()
            ->appendOutputTo(storage_path('logs/check-failed-messages-scheduler.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
