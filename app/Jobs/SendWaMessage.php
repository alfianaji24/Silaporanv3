<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Message;
use App\Models\Pengaturanumum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendWaMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan sebelum job dianggap gagal.
     */
    public int $tries = 3;

    protected string $phoneNumber;
    protected string $message;
    protected bool $birthday;

    public function __construct(string $phoneNumber, string $message, bool $birthday = false)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->birthday = $birthday;
    }

    public function handle(): void
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        if (!$generalsetting) {
            Log::warning('SendWaMessage: Pengaturan umum tidak ditemukan');
            $this->recordMessage('WhatsApp Gateway', $this->phoneNumber, 'failed', null, 'Pengaturan umum tidak ditemukan');
            return;
        }

        $apiKey = $generalsetting->wa_api_key;
        $domainWaGateway = $generalsetting->domain_wa_gateway;
        $providerWa = $generalsetting->provider_wa;
        $tujuanNotifikasi = $generalsetting->tujuan_notifikasi_wa;
        $penerima = $this->resolveRecipient($generalsetting);
        Log::info('SendWaMessage: Pengaturan umum', [
            'apiKey' => $apiKey,
            'domainWaGateway' => $domainWaGateway,
            'providerWa' => $providerWa,
            'tujuanNotifikasi' => $tujuanNotifikasi,
            'phoneNumber' => $this->phoneNumber,
        ]);
        Log::info('SendWaMessage: Penerima', [
            'penerima' => $penerima,
            'tujuanNotifikasi' => $tujuanNotifikasi,
            'id_group_wa' => $generalsetting->id_group_wa,
            'phoneNumber' => $this->phoneNumber,
        ]);
        //$penerima = $tujuanNotifikasi == 1 ? $generalsetting->id_group_wa : $this->phoneNumber;
        if (empty($penerima)) {
            Log::warning('SendWaMessage: Nomor penerima kosong', [
                'tujuanNotifikasi' => $tujuanNotifikasi,
                'phoneNumber' => $this->phoneNumber,
                'id_group_wa' => $generalsetting->id_group_wa,
            ]);
            $this->recordMessage($this->getSender($providerWa), '-', 'failed', null, 'Nomor penerima kosong');
            return;
        }

        if ($providerWa === 'fe') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $penerima,
                    'message' => $this->message,
                    'filename' => 'filename',
                    'schedule' => 0,
                    'typing' => true,
                    'delay' => '2',
                    'countryCode' => '62',
                    'followup' => 0,
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $apiKey
                ),
            ));

            $response = curl_exec($curl);
            $errno = curl_errno($curl);
            $err = $errno ? curl_error($curl) : null;
            $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            Log::info('SendWaMessage: Fonnte response', [
                'http' => $httpCode,
                'response' => $response,
                'errno' => $errno,
                'error' => $err,
            ]);

            if ($errno || $httpCode >= 400) {
                throw new \RuntimeException('SendWaMessage Fonnte gagal: ' . ($err ?: ('HTTP ' . $httpCode)));
            }

            $responseData = is_string($response) ? json_decode($response, true) : null;
            if (is_array($responseData) && $this->providerRejected($responseData)) {
                throw new \RuntimeException(
                    'SendWaMessage Fonnte gagal: ' . $this->getResponseError($responseData, 'Provider menolak pesan')
                );
            }

            $this->recordMessage(
                'Fonnte',
                $penerima,
                'success',
                $this->getMessageId($responseData),
                null
            );
            return;
        }

        // Susun URL seperti pada WagatewayController
        // Gunakan protokol yang sudah ada di domain_wa_gateway, jika tidak ada default ke http://
        $domain = (string) $domainWaGateway;
        if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
            $domain = 'http://' . $domain;
        }
        $url = rtrim($domain, '/') . '/send-message';
        $sender = Device::where('status', 1)->first();
        if (!$sender) {
            Log::warning('SendWaMessage: Device sender aktif tidak ditemukan');
            $this->recordMessage('WhatsApp Gateway', $penerima, 'failed', null, 'Device sender aktif tidak ditemukan');
            return;
        }

        $payload = [
            'api_key' => $apiKey,
            'sender' => $sender->number,
            'number' => $penerima,
            'message' => $this->message,
        ];

        // Gunakan JSON format untuk konsistensi dengan endpoint lain (info-device, generate-qr)
        // Beberapa gateway seperti asfimedia.id membutuhkan JSON format
        $response = Http::timeout(30)
            ->asJson()
            ->post($url, $payload);

        Log::info('SendWaMessage: Gateway response', [
            'http' => $response->status(),
            'response' => $response->body(),
            'payload' => $payload,
            'url' => $url,
            'sender' => $sender->number,
            'penerima' => $penerima,
            'message' => $this->message,
            'api_key' => $apiKey,
            'domain_wa_gateway' => $domainWaGateway,
            'provider_wa' => $providerWa,
            'tujuan_notifikasi' => $tujuanNotifikasi,
        ]);

        if (!$response->successful()) {
            $responseData = $response->json();
            throw new \RuntimeException(
                'SendWaMessage Gateway gagal: '
                . $this->getResponseError(
                    is_array($responseData) ? $responseData : [],
                    'HTTP ' . $response->status()
                )
            );
        }

        $responseData = $response->json();
        if (is_array($responseData) && $this->providerRejected($responseData)) {
            throw new \RuntimeException(
                'SendWaMessage Gateway gagal: '
                . $this->getResponseError($responseData, 'Provider menolak pesan')
            );
        }

        $this->recordMessage(
            (string) $sender->number,
            $penerima,
            'success',
            $this->getMessageId(is_array($responseData) ? $responseData : null),
            null
        );
    }

    public function failed(?Throwable $exception): void
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $recipient = $this->resolveRecipient($generalsetting);
        $sender = $this->getSender($generalsetting?->provider_wa);

        $this->recordMessage(
            $sender,
            $recipient !== '' ? $recipient : '-',
            'failed',
            null,
            $exception?->getMessage() ?? 'Pengiriman WhatsApp gagal'
        );
    }

    private function resolveRecipient(?Pengaturanumum $settings): string
    {
        if ($this->birthday || !$settings || $settings->tujuan_notifikasi_wa != 1) {
            return $this->phoneNumber;
        }

        return (string) $settings->id_group_wa;
    }

    private function getSender(?string $provider): string
    {
        if ($provider === 'fe') {
            return 'Fonnte';
        }

        return (string) (Device::where('status', 1)->value('number') ?: 'WhatsApp Gateway');
    }

    private function providerRejected(array $response): bool
    {
        foreach (['success', 'status'] as $key) {
            if (!array_key_exists($key, $response)) {
                continue;
            }

            $value = $response[$key];
            if ($value === false || $value === 0) {
                return true;
            }
            if (is_string($value) && in_array(strtolower($value), ['false', '0', 'failed', 'error', 'rejected'], true)) {
                return true;
            }
        }

        return false;
    }

    private function getResponseError(array $response, string $fallback): string
    {
        foreach (['message', 'error', 'detail'] as $key) {
            if (isset($response[$key]) && is_scalar($response[$key])) {
                return (string) $response[$key];
            }
        }

        return $fallback;
    }

    private function getMessageId(?array $response): ?string
    {
        if (!$response) {
            return null;
        }

        $messageId = $response['message_id']
            ?? $response['data']['message_id']
            ?? $response['id']
            ?? $response['data']['id']
            ?? null;

        if (is_array($messageId)) {
            $messageId = reset($messageId);
            if (is_array($messageId)) {
                $messageId = $messageId['id'] ?? $messageId['message_id'] ?? null;
            }
        }

        return is_scalar($messageId) ? substr((string) $messageId, 0, 255) : null;
    }

    private function recordMessage(
        string $sender,
        string $recipient,
        string $status,
        ?string $messageId,
        ?string $errorMessage
    ): void {
        try {
            Message::create([
                'pengirim' => substr($sender, 0, 255),
                'penerima' => substr($recipient, 0, 255),
                'pesan' => $this->message,
                'status' => $status,
                'message_id' => $messageId,
                'error_message' => $errorMessage,
            ]);
        } catch (Throwable $exception) {
            Log::error('SendWaMessage: Gagal menyimpan riwayat pesan WhatsApp', [
                'recipient' => $recipient,
                'status' => $status,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
