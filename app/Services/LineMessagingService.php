<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\LineMessagingServiceInterface;
use App\Models\AuthenticateOauth;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Http\Request;
use LINE\LINEBot\Exception\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Event\FollowEvent;

final class LineMessagingService implements LineMessagingServiceInterface
{
    private LINEBot $bot;

    public function __construct()
    {
        $httpClient = new CurlHTTPClient(config('services.line.channel_token'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.channel_secret')]);
    }

    /**
     * Webhookからのリクエストを処理する
     *
     * @param Request $request
     * @return void
     * @throws InvalidSignatureException
     */
    public function handleWebhook(Request $request): void
    {
        $signature = $request->header('X-Line-Signature');
        $content = $request->getContent();

        try {
            // 署名を検証
            $events = $this->bot->parseEventRequest($content, $signature);

            foreach ($events as $event) {
                $this->handleEvent($event);
            }
        } catch (InvalidSignatureException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error processing webhook: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * イベントを処理する
     *
     * @param \LINE\LINEBot\Event\BaseEvent $event
     * @return void
     */
    private function handleEvent($event): void
    {
        if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
            $this->handleTextMessage($event);
        } elseif ($event instanceof FollowEvent) {
            $this->handleFollowEvent($event);
        }
    }

    /**
     * フォローイベントを処理する
     *
     * @param FollowEvent $event
     * @return void
     */
    private function handleFollowEvent(FollowEvent $event): void
    {
        $userId = $event->getUserId();

        // ユーザープロフィールを取得
        $profile = $this->bot->getProfile($userId);

        if ($profile->isSucceeded()) {
            $profileData = $profile->getJSONDecodedBody();

            // AuthenticateOauthモデルを使用してデータを保存/更新
            AuthenticateOauth::updateOrCreate(
                [
                    'auth_provider_id' => config('services.line.provider_id'), // LINE用のプロバイダーID
                    'auth_code' => $userId, // LINEのユーザーIDを認証コードとして使用
                ],
                [
                    'token' => $profileData['userId'], // LINEのユーザーIDをトークンとして保存
                    'site_id' => config('services.line.site_id'), // サイトID
                    'entity_type' => 'user', // エンティティタイプ
                    'entity_id' => null, // 必要に応じて設定
                    'expires_at' => null, // LINEの場合は期限なし
                ]
            );

            // 歓迎メッセージを送信
            $this->pushMessage($userId, "フォローありがとうございます！\nよろしくお願いします。");
        }
    }

    /**
     * テキストメッセージを処理する
     *
     * @param \LINE\LINEBot\Event\MessageEvent\TextMessage $event
     * @return void
     */
    private function handleTextMessage($event): void
    {
        $replyToken = $event->getReplyToken();
        $text = $event->getText();

        // ここでメッセージの内容に応じた処理を実装
        // 例: エコーボット
        $response = $this->bot->replyText($replyToken, "受信したメッセージ: {$text}");

        if (!$response->isSucceeded()) {
            Log::error('Failed to reply message: ' . $response->getHTTPStatus() . ' ' . $response->getRawBody());
        }
    }

    /**
     * 登録済みのLINEユーザーIDを全て取得
     *
     * @return array
     */
    public function getAllLineUserIds(): array
    {
        return AuthenticateOauth::where('auth_provider_id', config('services.line.provider_id'))
            ->whereNull('deleted_at')
            ->pluck('auth_code')
            ->toArray();
    }

    /**
     * 特定のユーザーにメッセージを送信する
     *
     * @param string $userId
     * @param string $message
     * @return bool
     */
    public function pushMessage(string $userId, string $message): bool
    {
        $textMessageBuilder = new TextMessageBuilder($message);
        $response = $this->bot->pushMessage($userId, $textMessageBuilder);

        if (!$response->isSucceeded()) {
            Log::error('Failed to push message: ' . $response->getHTTPStatus() . ' ' . $response->getRawBody());
            return false;
        }

        return true;
    }

    /**
     * 複数のユーザーにメッセージを一斉送信する
     *
     * @param array $userIds
     * @param string $message
     * @return bool
     */
    public function multicast(array $userIds, string $message): bool
    {
        $textMessageBuilder = new TextMessageBuilder($message);
        $response = $this->bot->multicast($userIds, $textMessageBuilder);

        if (!$response->isSucceeded()) {
            Log::error('Failed to multicast message: ' . $response->getHTTPStatus() . ' ' . $response->getRawBody());
            return false;
        }

        return true;
    }

    /**
     * ユーザープロフィールを取得する
     *
     * @param string $userId
     * @return array|null
     */
    public function getProfile(string $userId): ?array
    {
        $profile = $this->bot->getProfile($userId);

        if ($profile->isSucceeded()) {
            return $profile->getJSONDecodedBody();
        }

        Log::error('Failed to get profile: ' . $profile->getHTTPStatus() . ' ' . $profile->getRawBody());
        return null;
    }
}
