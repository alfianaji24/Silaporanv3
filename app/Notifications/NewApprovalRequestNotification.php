<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Channels\FcmChannel;

class NewApprovalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $requestObj;
    private $featureName;
    private $karyawanName;
    private $level;

    /**
     * Create a new notification instance.
     */
    public function __construct($requestObj, $featureName, $karyawanName, $level)
    {
        $this->requestObj = $requestObj;
        $this->featureName = $featureName;
        $this->karyawanName = $karyawanName;
        $this->level = $level;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->hasRole('karyawan') && method_exists($notifiable, 'pushSubscriptions') && $notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }

        if (method_exists($notifiable, 'deviceTokens') && $notifiable->deviceTokens()->exists()) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $url = route('dashboard.index', [], false);

        if ($notifiable->hasRole('karyawan')) {
            $url = route('karyawan-approval.index', [], false);
        } else {
            // Admin / Superadmin direct links based on feature
            if ($this->featureName === 'Izin Absen') {
                $url = route('izinabsen.index', [], false);
            } elseif ($this->featureName === 'Izin Sakit') {
                $url = route('izinsakit.index', [], false);
            } elseif ($this->featureName === 'Izin Cuti') {
                $url = route('izincuti.index', [], false);
            } elseif ($this->featureName === 'Izin Dinas') {
                $url = route('izindinas.index', [], false);
            } elseif ($this->featureName === 'Reimbursement') {
                $url = route('reimbursement.index', [], false);
            } elseif ($this->featureName === 'Koreksi Absen') {
                $url = route('koreksipresensi.index', [], false);
            } else {
                $url = route('izinabsen.index', [], false);
            }
        }

        return [
            'title' => 'Ajuan Baru Menunggu Approval 🔔',
            'message' => 'Pengajuan ' . $this->featureName . ' baru dari ' . $this->karyawanName . ' memerlukan persetujuan Anda (Tahap ' . $this->level . ').',
            'url' => $url,
            'type' => 'new_approval',
            'icon' => 'ti-file'
        ];
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $icon = asset('assets/img/icon-192x192.png');
        $setting = \App\Models\Pengaturanumum::first();
        if ($setting && $setting->logo && \Illuminate\Support\Facades\Storage::exists('public/logo/' . $setting->logo)) {
            $icon = asset('storage/logo/' . $setting->logo);
        }

        return (new WebPushMessage)
            ->title('Ajuan Baru Menunggu Approval 🔔')
            ->body('Pengajuan ' . $this->featureName . ' baru dari ' . $this->karyawanName . ' memerlukan persetujuan Anda (Tahap ' . $this->level . ').')
            ->icon($icon)
            ->badge(asset('assets/img/icon-96x96.png'))
            ->data([
                'action_url' => route('karyawan-approval.index', [], false)
            ]);
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title'  => 'Ajuan Baru Menunggu Approval 🔔',
            'body'   => 'Pengajuan ' . $this->featureName . ' baru dari ' . $this->karyawanName . ' memerlukan persetujuan Anda (Tahap ' . $this->level . ').',
            'type'   => 'new_approval',
            'screen' => '/approval',
        ];
    }
}
