<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected ?Client $client;
    protected ?array $credentials = null;
    protected ?string $projectId = null;

    public function __construct()
    {
        $verify = app()->environment('production') ? true : false;
        $this->client = new Client(['verify' => $verify]);
        $this->loadCredentials();
    }

    /**
     * Load Firebase Service Account Credentials from storage or environment.
     */
    protected function loadCredentials(): void
    {
        $possiblePaths = [
            config('services.firebase.credentials'),
            storage_path('app/firebase/service-account.json'),
            base_path('config/firebase/service-account.json'),
            base_path('service-account.json'),
        ];

        foreach ($possiblePaths as $path) {
            if (!empty($path) && file_exists($path)) {
                $content = file_get_contents($path);
                $json = json_decode($content, true);
                if (is_array($json) && !empty($json['client_email']) && !empty($json['private_key'])) {
                    $this->credentials = $json;
                    $this->projectId = $json['project_id'] ?? env('FIREBASE_PROJECT_ID', 'silaporan_mobileapps');
                    return;
                }
            }
        }

        // Fallback to project_id from env if set
        $this->projectId = env('FIREBASE_PROJECT_ID', 'silaporan_mobileapps');
    }

    /**
     * Generate OAuth2 Access Token for Firebase HTTP v1 API.
     */
    public function getAccessToken(): ?string
    {
        if (!$this->credentials) {
            Log::warning('FCM: Firebase Service Account JSON credentials not found. Push notifications will be skipped until configured.');
            return null;
        }

        return Cache::remember('fcm_http_v1_access_token', 3500, function () {
            try {
                $now = time();
                $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
                $claim = json_encode([
                    'iss'   => $this->credentials['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud'   => 'https://oauth2.googleapis.com/token',
                    'exp'   => $now + 3600,
                    'iat'   => $now,
                ]);

                $base64UrlHeader = $this->base64UrlEncode($header);
                $base64UrlClaim = $this->base64UrlEncode($claim);
                $signatureData = $base64UrlHeader . '.' . $base64UrlClaim;

                $privateKey = $this->credentials['private_key'];
                $binarySignature = '';
                $success = openssl_sign($signatureData, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256);

                if (!$success) {
                    Log::error('FCM: Failed to sign OAuth2 JWT token for Firebase');
                    return null;
                }

                $jwt = $signatureData . '.' . $this->base64UrlEncode($binarySignature);

                $response = $this->client->post('https://oauth2.googleapis.com/token', [
                    'form_params' => [
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion'  => $jwt,
                    ],
                    'timeout' => 10,
                ]);

                $data = json_decode((string) $response->getBody(), true);
                return $data['access_token'] ?? null;
            } catch (\Exception $e) {
                Log::error('FCM: Error getting OAuth2 access token: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Send push notification to a specific User's registered mobile devices.
     *
     * @param  User  $user
     * @param  string  $title
     * @param  string  $body
     * @param  array  $data
     * @return array
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->deviceTokens()->pluck('fcm_token')->toArray();
        if (empty($tokens)) {
            return [
                'success' => true,
                'sent'    => 0,
                'message' => 'User has no registered mobile devices.',
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to multiple FCM tokens.
     *
     * @param  array  $tokens
     * @param  string  $title
     * @param  string  $body
     * @param  array  $data
     * @return array
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken || empty($this->projectId)) {
            return [
                'success' => false,
                'sent'    => 0,
                'message' => 'Firebase credentials not configured.',
            ];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $successCount = 0;
        $failedCount = 0;

        // Ensure all data values are string format for FCM HTTP v1
        $formattedData = [];
        foreach ($data as $key => $val) {
            $formattedData[(string) $key] = is_array($val) ? json_encode($val) : (string) $val;
        }

        foreach (array_unique($tokens) as $token) {
            if (empty($token)) {
                continue;
            }

            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $formattedData,
                    'android' => [
                        'priority'     => 'HIGH',
                        'notification' => [
                            'sound'                 => 'default',
                            'channel_id'            => 'high_importance_channel',
                            'notification_priority' => 'PRIORITY_HIGH',
                            'default_sound'         => true,
                            'default_vibrate_timings' => true,
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            try {
                $response = $this->client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type'  => 'application/json; UTF-8',
                    ],
                    'json'    => $payload,
                    'timeout' => 8,
                ]);

                if ($response->getStatusCode() === 200) {
                    $successCount++;
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $failedCount++;
                $responseBody = (string) $e->getResponse()->getBody();
                Log::warning("FCM: ClientException sending to token: {$responseBody}");

                // If token is invalid or unregistered, clean up database
                if (str_contains($responseBody, 'UNREGISTERED') || str_contains($responseBody, 'NOT_FOUND')) {
                    UserDeviceToken::where('fcm_token', $token)->delete();
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("FCM: Failed sending to token: " . $e->getMessage());
            }
        }

        return [
            'success' => $successCount > 0,
            'sent'    => $successCount,
            'failed'  => $failedCount,
        ];
    }

    /**
     * Helper to encode URL safe Base64.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
