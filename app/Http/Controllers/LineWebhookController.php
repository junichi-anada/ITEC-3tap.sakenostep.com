<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Messaging\DTOs\LineWebhookData;
use App\Services\Messaging\Actions\PushMessageAction;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    private $channelSecret;
    private $pushMessageAction;

    public function __construct(PushMessageAction $pushMessageAction)
    {
        $this->channelSecret = env('LINE_CHANNEL_SECRET');
        $this->pushMessageAction = $pushMessageAction;
    }

    public function handle(Request $request)
    {
        // LineWebhookDataオブジェクトを作成
        $webhookData = LineWebhookData::fromRequest($request);

        // 署名の検証
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
        $hash = hash_hmac('sha256', $content, $this->channelSecret, true);
        $expectedSignature = base64_encode($hash);
        return hash_equals($expectedSignature, $signature);
    }

    private function handleEvent(array $event)
    {
        $eventType = LineWebhookData::getEventType($event);
        $userId = LineWebhookData::getUserId($event);

        switch ($eventType) {
            case 'message':
                $this->handleMessageEvent($event, $userId);
                break;
            case 'follow':
                $this->handleFollowEvent($userId);
                break;
            default:
                Log::info('Unhandled event type: ' . $eventType);
                break;
        }
    }

    private function handleMessageEvent(array $event, ?string $userId)
    {
        // メッセージイベントの処理ロジックを実装
        $messageType = $event['message']['type'];
        $messageText = $event['message']['text'] ?? '';

        Log::info('Received message event from user: ' . $userId);
        Log::info('Message type: ' . $messageType);
        Log::info('Message text: ' . $messageText);

        // ここでメッセージに対する応答を実装
        // 例: メッセージ内容に応じて返信を送信する
    }

    private function handleFollowEvent(?string $userId)
    {
        // 友達登録イベントの処理ロジックを実装
        Log::info('Received follow event from user: ' . $userId);

        // サンクスメッセージを送信
        $message = '友達登録ありがとうございます！';
        $this->PushMessageAction->sendMessage($userId, $message);
    }

}
