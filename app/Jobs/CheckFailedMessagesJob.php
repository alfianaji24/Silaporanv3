<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Pengaturanumum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckFailedMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const MAX_LISTED = 10;

    public function handle(): void
    {
        $failedQuery = Message::where('status', 'failed')
            ->where('created_at', '>=', now()->subHours(1));

        $totalFailed = (clone $failedQuery)->count();

        if ($totalFailed === 0) {
            return;
        }

        $failedList = (clone $failedQuery)
            ->orderBy('created_at', 'asc')
            ->limit(self::MAX_LISTED)
            ->get();

        Log::info('CheckFailedMessagesJob: Ditemukan pesan gagal', ['count' => $totalFailed]);

        $message = $this->buildNotificationMessage($totalFailed, $failedList);

        $setting = Pengaturanumum::find(1);
        $adminPhone = normalizePhoneNumber($setting?->no_hp_wa ?? '');

        if (empty($adminPhone)) {
            Log::warning('CheckFailedMessagesJob: no_hp_wa admin kosong, notifikasi tidak dikirim');

            return;
        }

        dispatch(new SendWaMessage($adminPhone, $message));

        Log::info('CheckFailedMessagesJob: Notifikasi dikirim ke admin', [
            'total_failed' => $totalFailed,
            'listed' => $failedList->count(),
        ]);
    }

    private function buildNotificationMessage(int $totalFailed, $failedList): string
    {
        $lines = [
            '⚠️ NOTIFIKASI PESAN GAGAL TERKIRIM',
            '',
            "📊 Ada {$totalFailed} pesan gagal terkirim (1 jam terakhir)",
            '📋 Urutan: terlama → terbaru',
            '',
        ];

        foreach ($failedList as $index => $item) {
            $no = $index + 1;
            $waktu = $item->created_at->format('d/m/Y H:i');
            $preview = Str::limit(preg_replace('/\s+/', ' ', strip_tags($item->pesan ?? '')), 60);
            $error = Str::limit($item->error_message ?? '-', 80);

            $lines[] = "{$no}. [{$waktu}] → {$item->penerima}";
            $lines[] = "   Pesan: {$preview}";
            $lines[] = "   Error: {$error}";
            $lines[] = '';
        }

        if ($totalFailed > self::MAX_LISTED) {
            $sisa = $totalFailed - self::MAX_LISTED;
            $lines[] = "... dan {$sisa} pesan gagal lainnya.";
            $lines[] = '';
        }

        $lines[] = '📌 Action: WA Gateway → Riwayat Pesan → Gagal Terkirim';
        $lines[] = '🔗 Kirim ulang pesan yang masih perlu diproses.';

        return implode("\n", $lines);
    }
}
