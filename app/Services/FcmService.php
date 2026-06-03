<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    private const CACHE_KEY     = 'fcm_access_token';
    private const TOKEN_TTL_MIN = 55;

    public static function send(User $user, string $title, string $body, array $data = [], ?string $imageUrl = null): void
    {
        if (empty($user->fcm_token)) {
            Log::info("FCM: User [{$user->id}] tidak punya fcm_token, notif dilewati.");
            return;
        }

        try {
            $accessToken = self::getAccessToken();
            $projectId   = self::getProjectId();

            if (!$accessToken || !$projectId) {
                Log::warning('FCM: Tidak bisa mendapatkan access token atau project ID. Pastikan service account sudah dikonfigurasi.');
                return;
            }

            $stringData = array_map('strval', $data);

            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $user->fcm_token,
                        'notification' => array_filter([
                            'title' => $title,
                            'body'  => $body,
                            'image' => $imageUrl,
                        ]),
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'sound'        => 'default',
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ],
                        ],
                        'data' => $stringData,
                    ],
                ]);

            if ($response->failed()) {
                Log::warning("FCM: Gagal kirim ke user [{$user->id}]. Status: {$response->status()}. Body: " . $response->body());

                if ($response->status() === 404 || str_contains($response->body(), 'UNREGISTERED')) {
                    $user->update(['fcm_token' => null]);
                    Log::info("FCM: Token user [{$user->id}] dihapus karena tidak terdaftar.");
                }
            } else {
                Log::info("FCM: Berhasil kirim notif ke user [{$user->id}].");
            }
        } catch (\Exception $e) {
            Log::error("FCM: Exception saat kirim notif ke user [{$user->id}]: " . $e->getMessage());
        }
    }

    public static function sendToMany(iterable $users, string $title, string $body, array $data = [], ?string $imageUrl = null): void
    {
        foreach ($users as $user) {
            self::send($user, $title, $body, $data, $imageUrl);
        }
    }

    private static function getAccessToken(): ?string
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::TOKEN_TTL_MIN), function () {
            $credentials = self::loadServiceAccount();
            if (!$credentials) return null;

            $jwt = self::buildJwt($credentials);
            if (!$jwt) return null;

            $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->failed()) {
                Log::error('FCM: Gagal tukar JWT dengan access token: ' . $response->body());
                return null;
            }

            return $response->json('access_token');
        });
    }

    private static function loadServiceAccount(): ?array
    {
        $path = storage_path('app/projek-magang-bps-d47e4efb4387.json');

        if (!file_exists($path)) {
            Log::warning("FCM: File service account tidak ditemukan di: {$path}");
            return null;
        }

        $content = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('FCM: File service account tidak valid (bukan JSON yang valid).');
            return null;
        }

        return $content;
    }

    private static function getProjectId(): ?string
    {
        $credentials = self::loadServiceAccount();
        return $credentials['project_id'] ?? null;
    }

    private static function buildJwt(array $credentials): ?string
    {
        if (empty($credentials['private_key']) || empty($credentials['client_email'])) {
            Log::error('FCM: Service account JSON tidak lengkap (missing private_key atau client_email).');
            return null;
        }

        $now = time();

        $header = self::base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $payload = self::base64UrlEncode(json_encode([
            'iss'   => $credentials['client_email'],
            'sub'   => $credentials['client_email'],
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ]));

        $signingInput = "{$header}.{$payload}";
        $privateKey   = openssl_pkey_get_private($credentials['private_key']);

        if (!$privateKey) {
            Log::error('FCM: Gagal memuat private key dari service account.');
            return null;
        }

        $signature = '';
        if (!openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            Log::error('FCM: Gagal membuat signature JWT.');
            return null;
        }

        return "{$signingInput}." . self::base64UrlEncode($signature);
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
