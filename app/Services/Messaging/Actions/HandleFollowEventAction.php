<?php

namespace App\Services\Messaging\Actions;

use App\Models\AuthenticateOauth;
use App\Services\Messaging\DTOs\LineProfileData;
use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use Illuminate\Support\Facades\Log;

class HandleFollowEventAction
{
    use ServiceErrorHandler;

    public function __construct(
        private LINEBot $bot,
        private PushMessageAction $pushMessageAction
    ) {}

    /**
     * フォローイベントを処理する
     *
     * @param FollowEvent $event
     * @return void
     * @throws LineMessagingException
     */
    public function execute(FollowEvent $event): void
    {
        $this->tryCatchWrapper(
            function () use ($event) {
                $userId = $event->getUserId();
                Log::info("Processing follow event for user: {$userId}");

                // ユーザープロフィールを取得
                $profile = $this->bot->getProfile($userId);
                if (!$profile->isSucceeded()) {
                    throw LineMessagingException::profileGetFailed($userId, $profile->getHTTPStatus());
                }

                // プロフィールデータをDTOに変換
                $profileData = LineProfileData::fromLineResponse($profile->getJSONDecodedBody());

                // AuthenticateOauthモデルを使用してデータを保存/更新
                $this->saveAuthenticateOauth($profileData);

                // 歓迎メッセージを送信
                $this->sendWelcomeMessage($userId);
            },
            'フォローイベントの処理に失敗しました'
        );
    }

    /**
     * AuthenticateOauthデータを保存する
     *
     * @param LineProfileData $profileData
     * @return void
     */
    private function saveAuthenticateOauth(LineProfileData $profileData): void
    {
        AuthenticateOauth::updateOrCreate(
            [
                'auth_provider_id' => config('services.line.provider_id'),
                'auth_code' => $profileData->userId,
            ],
            $profileData->toAuthenticateOauthData(
                config('services.line.provider_id'),
                config('services.line.site_id')
            )
        );
    }

    /**
     * 歓迎メッセージを送信する
     *
     * @param string $userId
     * @return void
     * @throws LineMessagingException
     */
    private function sendWelcomeMessage(string $userId): void
    {
        $this->pushMessageAction->execute($userId, "フォローありがとうございます！\nよろしくお願いします。");
    }
}
