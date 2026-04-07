<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckFailedMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Cek pesan gagal dalam 1 jam terakhir
        $failedMessages = Message::where('status', 'failed')
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        if ($failedMessages > 0) {
            Log::info('CheckFailedMessagesJob: Ditemukan pesan gagal', ['count' => $failedMessages]);

            $message = "⚠️ NOTIFIKASI PESAN GAGAL TERKIRIM\n\n"
                . "📊 Status: Ada {$failedMessages} pesan yang gagal terkirim dalam 1 jam terakhir\n\n"
                . "📌 Action: Silakan buka halaman Riwayat Pesan WhatsApp dan klik 'Gagal Terkirim' untuk melihat pesan yang perlu dikirim ulang\n\n"
                . "🔗 Akses: Menu WA Gateway → Riwayat Pesan";

            // Kirim notifikasi ke admin
            dispatch(new SendWaMessage('085162663451', $message));

            Log::info('CheckFailedMessagesJob: Notifikasi dikirim ke admin');
        }
    }
}
