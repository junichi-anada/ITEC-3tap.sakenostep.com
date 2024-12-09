<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Messaging\DTOs\LineWebhookData;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // LineWebhookDataオブジェクトを作成
        $webhookData = LineWebhookData::fromRequest($request);

        // 署名の検証（必要に応じて実装）
        if (!$this->isValidSignature($webhookData->signature, $webhookData->content)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // イベントごとに処理を実行
        foreach ($webhookData->events as $event) {
            $this->handleEvent($event);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    private function isValidSignature($signature, $content)
    {
        // 署名の検証ロジックを実装
        // 例: hash_hmac('sha256', $content, $channelSecret) === $signature
        return true;
    }

    private function handleEvent(array $event)
    {
        $eventType = LineWebhookData::getEventType($event);
        $userId = LineWebhookData::getUserId($event);

        switch ($eventType) {
            case 'message':
                $this->handleMessageEvent($event, $userId);
                break;
            // 他のイベントタイプに対する処理を追加
            default:
                Log::info('Unhandled event type: ' . $eventType);
                break;
        }
    }

    private function handleMessageEvent(array $event, ?string $userId)
    {
        // メッセージイベントの処理ロジックを実装
        Log::info('Received message event from user: ' . $userId);
    }
}
