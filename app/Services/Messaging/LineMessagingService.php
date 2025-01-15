<?php

declare(strict_types=1);

namespace App\Services\Messaging;

use App\Contracts\LineMessagingServiceInterface;
use App\Models\AuthenticateOauth;
use App\Services\Messaging\Actions\HandleWebhookAction;
use App\Services\Messaging\Actions\PushMessageAction;
use App\Services\Messaging\Actions\MulticastMessageAction;
use App\Services\Messaging\DTOs\LineMessageData;
use App\Services\Messaging\DTOs\LineWebhookData;
use App\Services\ServiceErrorHandler;
use Illuminate\Http\Request;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\MulticastRequest;
use LINE\Clients\MessagingApi\Model\GetProfileResponse;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Constants\MessageType;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * LINEメッセージングサービスクラス
 *
 * このクラスはLINEメッセージングに関する操作のファサードとして機能し、
 * 具体的な処理を各Actionクラスに委譲します。
 */
final class LineMessagingService implements LineMessagingServiceInterface
{
    use ServiceErrorHandler;

    public function __construct(
        private MessagingApiApi $messagingApi,
        private HandleWebhookAction $handleWebhookAction,
        private PushMessageAction $pushMessageAction,
        private MulticastMessageAction $multicastMessageAction
    ) {
        // アクセストークンの設定を確実に行う
        $token = config('services.line.channel_access_token');
        if (empty($token)) {
            throw new \RuntimeException('LINE Channel Access Token is not configured');
        }
        
        // 新しいConfigurationインスタンスを作成
        $config = new \LINE\Clients\MessagingApi\Configuration();
        $config->setAccessToken($token);
        
        // 新しいMessagingApiApiインスタンスを作成
        $this->messagingApi = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
            client: new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]),
            config: $config
        );
        
        Log::info('LINE Messaging API Configuration', [
            'token_exists' => !empty($token),
            'token_length' => strlen($token),
            'headers' => [
                'Authorization' => 'Bearer ' . substr($token, 0, 10) . '...'
            ]
        ]);
    }

    /**
     * Webhookからのリクエストを処理する
     *
     * @param Request $request
     * @return void
     */
    public function handleWebhook(Request $request): void
    {
        $webhookData = LineWebhookData::fromRequest($request);
        $this->handleWebhookAction->execute($webhookData);
    }

    /**
     * 登録済みのLINEユーザーID一覧を取得
     *
     * @return array
     */
    public function getAllLineUserIds(): array
    {
        return $this->tryCatchWrapper(
            fn () => AuthenticateOauth::where('auth_provider_id', config('services.line.provider_id'))
                ->whereNull('deleted_at')
                ->pluck('auth_code')
                ->toArray(),
            'LINEユーザーIDの取得に失敗しました'
        );
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
        try {
            $textMessage = new TextMessage([
                'type' => MessageType::TEXT,
                'text' => $message
            ]);

            $request = new PushMessageRequest([
                'to' => $userId,
                'messages' => [$textMessage]
            ]);

            $this->messagingApi->pushMessage($request);
            return true;

        } catch (\Exception $e) {
            Log::error('プッシュメッセージ送信エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'message' => $message
            ]);
            return false;
        }
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
        try {
            $textMessage = (new TextMessage())
                ->setType(MessageType::TEXT)
                ->setText($message);

            $request = (new MulticastRequest())
                ->setTo($userIds)
                ->setMessages([$textMessage]);

            $this->messagingApi->multicast($request);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to multicast message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ユーザープロフィールを取得する
     *
     * @param string $userId
     * @return array|null
     */
    public function getProfile(string $userId): ?array
    {
        return $this->tryCatchWrapper(
            function () use ($userId) {
                /** @var GetProfileResponse $profile */
                $profile = $this->messagingApi->getProfile($userId);
                return [
                    'displayName' => $profile->getDisplayName(),
                    'userId' => $profile->getUserId(),
                    'pictureUrl' => $profile->getPictureUrl(),
                    'statusMessage' => $profile->getStatusMessage()
                ];
            },
            'プロフィールの取得に失敗しました',
            ['user_id' => $userId]
        );
    }

    /**
     * LINEのリンクトークンを取得
     *
     * @param string $userId
     * @return string|null
     */
    public function getLinkToken(string $userId): ?string
    {
        try {
            $response = $this->messagingApi->issueLinkToken($userId);
            return $response->getLinkToken();
        } catch (\Exception $e) {
            Log::error('リンクトークン取得エラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * リンクトークンを発行する
     *
     * @param string $userId LINEユーザーID
     * @return string
     */
    public function issueLinkToken(string $userId): string
    {
        return $this->tryCatchWrapper(
            fn () => $this->messagingApi->issueLinkToken($userId)->getLinkToken(),
            'リンクトークンの発行に失敗しました',
            ['user_id' => $userId]
        );
    }

    /**
     * アカウントを連携する
     *
     * @param string $linkToken
     * @param int $userId
     * @return bool
     */
    public function linkAccount(string $linkToken, int $userId): bool
    {
        return $this->tryCatchWrapper(
            function () use ($linkToken, $userId) {
                // リンクトークンの検証
                $tokenData = json_decode(base64_decode($linkToken), true);
                if (!$tokenData ||
                    !isset($tokenData['nonce']) ||
                    !isset($tokenData['timestamp']) ||
                    !isset($tokenData['channel_id'])) {
                    return false;
                }

                // タイムスタンプの検証（10分以内）
                if (time() - $tokenData['timestamp'] > 600) {
                    return false;
                }

                // チャンネルIDの検証
                if ($tokenData['channel_id'] !== config('services.line.channel_id')) {
                    return false;
                }

                // アカウント連携情報を保存
                AuthenticateOauth::create([
                    'user_id' => $userId,
                    'auth_provider_id' => config('services.line.provider_id'),
                    'auth_code' => $tokenData['nonce'],
                    'site_id' => config('services.line.site_id')
                ]);

                return true;
            },
            'アカウント連携に失敗しました',
            ['user_id' => $userId]
        );
    }

    /**
     * アカウント連携を解除する
     *
     * @param string $userId
     * @return bool
     */
    public function unlinkAccount(string $userId): bool
    {
        return $this->tryCatchWrapper(
            function () use ($userId) {
                return AuthenticateOauth::where('auth_code', $userId)
                    ->where('auth_provider_id', config('services.line.provider_id'))
                    ->delete() > 0;
            },
            'アカウント連携解除に失敗しました',
            ['user_id' => $userId]
        );
    }

    /**
     * リプライメッセージを送信する
     *
     * @param ReplyMessageRequest $request
     * @return bool
     */
    public function replyMessage(ReplyMessageRequest $request): bool
    {
        return $this->tryCatchWrapper(
            function () use ($request) {
                $this->messagingApi->replyMessage($request);
                return true;
            },
            'リプライメッセージの送信に失敗しました',
            ['reply_token' => $request->getReplyToken()]
        );
    }
}
