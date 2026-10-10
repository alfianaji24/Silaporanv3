<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Channels\FcmChannel;

class TestPushNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        $channels = [];

        if (method_exists($notifiable, 'pushSubscriptions') && $notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }

        if (method_exists($notifiable, 'deviceTokens') && $notifiable->deviceTokens()->exists()) {
            $channels[] = FcmChannel::class;
        }

        // Fallback if neither exists yet
        if (empty($channels)) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Halo dari Presensi GPS! 👋')
            ->body('Ini adalah notifikasi uji coba push dari PWA Anda.')
            ->icon('/assets/img/icon-192x192.png')
            ->badge('/assets/img/icon-96x96.png')
            ->data([
                'action_url' => route('dashboard.index', [], false)
            ]);
    }

    public function toFcm($notifiable)
    {
        return [
            'title'  => 'Halo dari Silaporan Apps! 👋',
            'body'   => 'Ini adalah notifikasi uji coba push ke perangkat mobile Anda.',
            'type'   => 'test',
            'screen' => '/dashboard',
        ];
    }
}

