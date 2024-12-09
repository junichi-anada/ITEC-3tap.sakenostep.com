<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Messaging\DTOs\LineWebhookData;
use App\Services\Messaging\Actions\PushMessageAction;
use Illuminate\Support\Facades\Log;

/**
 * LINE Webhookを処理するコントローラー
 *
 * @package App\Http\Controllers
 */
class LineWebhookController extends Controller
{
    private $channelSecret;
    private $pushMessageAction;

    /**
     * コンストラクタ
     *
     * @param PushMessageAction $pushMessageAction メッセージ送信アクション
     */
    public function __construct(PushMessageAction $pushMessageAction)
    {
        $this->channelSecret = env('LINE_CHANNEL_SECRET');
        $this->pushMessageAction = $pushMessageAction;
    }

    /**
     * Webhookリクエストを処理する
     *
     * @param Request $request HTTPリクエスト
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * 署名を検証する
     *
     * @param string $signature 署名
     * @param string $content コンテンツ
     * @return bool
     */
    private function isValidSignature($signature, $content)
    {
        $hash = hash_hmac('sha256', $content, $this->channelSecret, true);
        $expectedSignature = base64_encode($hash);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * イベントを処理する
     *
     * @param array $event イベントデータ
     * @return void
     */
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

    /**
     * メッセージイベントを処理する
     *
     * @param array $event イベントデータ
     * @param string|null $userId ユーザーID
     * @return void
     */
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

    /**
     * フォローイベントを処理する
     *
     * @param string|null $userId ユーザーID
     * @return void
     */
    private function handleFollowEvent(?string $userId)
    {
        // 友達登録イベントの処理ロジックを実装
        Log::info('Received follow event from user: ' . $userId);

        // サンクスメッセージを送信
        $message = '友達登録ありがとうございます！';
        $this->pushMessageAction->sendMessage($userId, $message);
    }
}
