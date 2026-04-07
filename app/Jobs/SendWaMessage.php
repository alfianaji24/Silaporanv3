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

class SendWaMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan sebelum job dianggap gagal.
     */
    public int $tries = 1;

    public ?int $messageLogId = null;

    protected string $phoneNumber;
    protected string $message;
    protected bool $birthday;

    public function __construct(string $phoneNumber, string $message, bool $birthday = false, ?int $messageLogId = null)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->birthday = $birthday;
        $this->messageLogId = $messageLogId;

        if (!$this->messageLogId) {
            $messageLog = Message::create([
                'pengirim' => '-',
                'penerima' => $phoneNumber,
                'pesan' => $message,
                'status' => 'pending',
                'message_id' => null,
                'error_message' => null,
                'attempts' => 0,
                'permanent_failed' => false,
            ]);

            $this->messageLogId = $messageLog->id;
        }
    }

    public function handle(): void
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        if (!$generalsetting) {
            Log::warning('SendWaMessage: Pengaturan umum tidak ditemukan');
            return;
        }

        $apiKey = $generalsetting->wa_api_key;
        $domainWaGateway = $generalsetting->domain_wa_gateway;
        $providerWa = $generalsetting->provider_wa;
        $tujuanNotifikasi = $generalsetting->tujuan_notifikasi_wa;
        Log::info('SendWaMessage: Pengaturan umum', [
            'apiKey' => $apiKey,
            'domainWaGateway' => $domainWaGateway,
            'providerWa' => $providerWa,
            'tujuanNotifikasi' => $tujuanNotifikasi,
            'phoneNumber' => $this->phoneNumber,
        ]);
        if ($this->birthday) {
            $penerima = $this->phoneNumber;
        } else {
            $penerima = $tujuanNotifikasi == 1 ? $generalsetting->id_group_wa : $this->phoneNumber;
        }

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
            $messageLog = Message::find($this->messageLogId);
            if ($messageLog) {
                $this->updateMessageLogFailure($messageLog, 'Nomor penerima kosong');
            }
            throw new \RuntimeException('SendWaMessage: Nomor penerima kosong');
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
                $errorMessage = ($err ?: ('HTTP ' . $httpCode)) . (is_string($response) && $response !== '' ? ' | ' . $response : '');
                $messageLog = Message::find($this->messageLogId);
                if ($messageLog) {
                    $this->updateMessageLogFailure($messageLog, $errorMessage);
                }
                throw new \RuntimeException('SendWaMessage Fonnte gagal: ' . $errorMessage);
            }
            $messageId = null;
            if (is_string($response) && $response !== '') {
                $decoded = json_decode($response, true);
                if (is_array($decoded)) {
                    $messageId = $decoded['message_id'] ?? $decoded['id'] ?? null;
                    if ($messageId !== null) {
                        $messageId = (string) $messageId;
                    }
                }
            }
            $messageLog = Message::find($this->messageLogId);
            if ($messageLog) {
                $messageLog->pengirim = 'fonnte';
                $messageLog->status = 'success';
                $messageLog->message_id = $messageId;
                $messageLog->error_message = null;
                $messageLog->permanent_failed = false;
                $messageLog->last_attempt_at = now();
                $messageLog->save();
            }
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
            $messageLog = Message::find($this->messageLogId);
            if ($messageLog) {
                $this->updateMessageLogFailure($messageLog, 'Device sender aktif tidak ditemukan (devices.status=1)');
            }
            throw new \RuntimeException('SendWaMessage: Device sender aktif tidak ditemukan');
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

        if ($response->successful()) {
            $responseData = $response->json();
            $messageLog = Message::find($this->messageLogId);
            if ($messageLog) {
                $messageLog->pengirim = $sender->number;
                $messageLog->status = 'success';
                $messageLog->message_id = is_array($responseData) ? ($responseData['message_id'] ?? null) : null;
                $messageLog->error_message = null;
                $messageLog->permanent_failed = false;
                $messageLog->last_attempt_at = now();
                $messageLog->save();
            }
            return;
        }

        $errorResponse = $response->json();
        $statusCode = $response->status();
        $errText = is_array($errorResponse)
            ? ($errorResponse['message'] ?? json_encode($errorResponse, JSON_UNESCAPED_UNICODE))
            : $response->body();
        $errorMessage = $errText !== '' ? $errText : "HTTP {$statusCode}";
        $messageLog = Message::find($this->messageLogId);
        if ($messageLog) {
            $this->updateMessageLogFailure($messageLog, $errorMessage);
        }
        throw new \RuntimeException('SendWaMessage Gateway gagal: HTTP ' . $response->status());
    }

    protected function updateMessageLogFailure(Message $messageLog, string $errorMessage): void
    {
        $messageLog->attempts += 1;
        $messageLog->last_attempt_at = now();
        $messageLog->status = 'failed';
        $messageLog->error_message = $errorMessage;
        if ($messageLog->attempts >= 1) {
            $messageLog->permanent_failed = true;
        }
        $messageLog->save();
    }
}
