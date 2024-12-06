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
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Illuminate\Support\Facades\Log;

/**
 * LINEメッセージングサービスクラス
 *
 * このクラスはLINEメッセージングに関する操作のファサードとして機能し、
 * 具体的な処理を各Actionクラスに委譲します。
 */
final class LineMessagingService implements LineMessagingServiceInterface
{
    use ServiceErrorHandler;

    private LINEBot $bot;

    public function __construct(
        private HandleWebhookAction $handleWebhookAction,
        private PushMessageAction $pushMessageAction,
        private MulticastMessageAction $multicastMessageAction
    ) {
        $httpClient = new CurlHTTPClient(config('services.line.channel_token'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.channel_secret')]);
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
            $this->pushMessageAction->execute($userId, $message);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to push message: ' . $e->getMessage());
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
            $this->multicastMessageAction->executeInChunks($userIds, $message);
            return true;
        } catch (\Exception $e) {
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
                $profile = $this->bot->getProfile($userId);
                return $profile->isSucceeded() ? $profile->getJSONDecodedBody() : null;
            },
            'プロフィールの取得に失敗しました',
            ['user_id' => $userId]
        );
    }
}
