<?php

namespace App\Channels;

use App\Services\FcmService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notifiable, 'deviceTokens') || $notifiable->deviceTokens()->count() === 0) {
            return;
        }

        try {
            $data = [];
            if (method_exists($notification, 'toFcm')) {
                $data = $notification->toFcm($notifiable);
            } elseif (method_exists($notification, 'toArray')) {
                $data = $notification->toArray($notifiable);
            }

            if (empty($data)) {
                return;
            }

            $title = $data['title'] ?? 'Presensi GPS Notifikasi';
            $body = $data['body'] ?? ($data['message'] ?? '');
            
            // Collect extra payload
            $payload = [
                'type' => $data['type'] ?? 'general',
                'url'  => $data['url'] ?? '',
                'id'   => isset($data['id']) ? (string)$data['id'] : '',
            ];
            if (isset($data['action_url'])) {
                $payload['action_url'] = (string)$data['action_url'];
            }
            if (isset($data['screen'])) {
                $payload['screen'] = (string)$data['screen'];
            }

            $this->fcmService->sendToUser($notifiable, $title, $body, $payload);
        } catch (\Exception $e) {
            Log::error('FcmChannel: Error sending notification: ' . $e->getMessage());
        }
    }
}
