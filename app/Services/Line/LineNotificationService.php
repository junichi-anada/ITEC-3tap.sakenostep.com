<?php

namespace App\Services\Line;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotificationService
{
    /**
     * LINEメッセージを送信する
     *
     * @param string $lineUserId LINE UserID
     * @param string $message 送信するメッセージ
     * @return bool 送信成功時true
     * @throws \Exception 送信失敗時
     */
    public function sendMessage(string $lineUserId, string $message): bool
    {
        try {
            $token = config('services.line.channel_access_token');
            if (empty($token)) {
                throw new \Exception('LINE Channel Access Token is not configured');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.line.me/v2/bot/message/push', [
                'to' => $lineUserId,
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $message
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('LINE APIエラー', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'headers' => $response->headers()
                ]);
                throw new \Exception('LINE API error: ' . $response->body());
            }

            return true;

        } catch (\Exception $e) {
            Log::error('LINEメッセージ送信エラー', [
                'line_user_id' => $lineUserId,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 