<?php

namespace App\Services\Messaging;

use App\Contracts\LineMessagingServiceInterface;
use App\Models\LineUser;
use App\Models\User;
use App\Models\Site;
use App\Models\AuthProvider;
use App\Models\AuthenticateOauth;
use App\Services\Messaging\Exceptions\LineMessagingException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LineAccountLinkService
{
    private LineMessagingServiceInterface $lineMessaging;

    public function __construct(LineMessagingServiceInterface $lineMessaging)
    {
        $this->lineMessaging = $lineMessaging;
    }

    /**
     * アカウント連携URLを生成
     */
    public function generateLinkUrl(Site $site, string $lineUserId): string
    {
        try {
            $lineUser = LineUser::firstOrCreate(
                ['site_id' => $site->id, 'line_user_id' => $lineUserId],
                ['display_name' => '未設定']
            );

            $nonce = $lineUser->generateNonce();
            
            // LINE Login用のチャネルIDを取得
            $authProvider = AuthProvider::where('provider_name', 'line')->first();
            $siteAuthProvider = $site->siteAuthProviders()
                ->where('auth_provider_id', $authProvider->id)
                ->first();

            if (!$siteAuthProvider) {
                throw new LineMessagingException('LINE Login設定が見つかりません');
            }

            // アカウント連携URLを生成
            $params = http_build_query([
                'response_type' => 'code',
                'client_id' => $siteAuthProvider->client_id,
                'redirect_uri' => route('line.callback'),
                'state' => $nonce,
                'scope' => 'profile',
                'bot_prompt' => 'aggressive'
            ]);

            return "https://access.line.me/oauth2/v2.1/authorize?{$params}";

        } catch (\Exception $e) {
            Log::error('アカウント連携URL生成エラー: ' . $e->getMessage());
            throw new LineMessagingException('アカウント連携URLの生成に失敗しました');
        }
    }

    /**
     * アカウント連携のコールバック処理
     */
    public function handleCallback(string $code, string $state, string $error = null): bool
    {
        if ($error) {
            Log::error('LINE連携エラー: ' . $error);
            throw new LineMessagingException('LINE連携処理でエラーが発生しました');
        }

        try {
            $lineUser = LineUser::where('nonce', $state)->firstOrFail();

            if (!$lineUser->verifyNonce($state)) {
                throw new LineMessagingException('不正なリクエストです');
            }

            // アクセストークンを取得
            $token = $this->lineMessaging->getAccessToken($code);
            
            // LINEプロフィール情報を取得
            $profile = $this->lineMessaging->getProfile($token);

            DB::transaction(function () use ($lineUser, $token, $profile) {
                // LINE認証情報を保存
                $authProvider = AuthProvider::where('provider_name', 'line')->first();
                
                AuthenticateOauth::updateOrCreate(
                    [
                        'site_id' => $lineUser->site_id,
                        'entity_type' => LineUser::class,
                        'entity_id' => $lineUser->id,
                        'auth_provider_id' => $authProvider->id
                    ],
                    [
                        'token' => $token,
                        'expires_at' => now()->addDays(30)
                    ]
                );

                // LINEユーザー情報を更新
                $lineUser->update([
                    'display_name' => $profile['displayName'],
                    'picture_url' => $profile['pictureUrl'] ?? null,
                    'status_message' => $profile['statusMessage'] ?? null
                ]);
            });

            return true;

        } catch (\Exception $e) {
            Log::error('LINE連携コールバックエラー: ' . $e->getMessage());
            throw new LineMessagingException('LINE連携処理に失敗しました');
        }
    }

    /**
     * アカウント連携解除
     */
    public function unlinkAccount(Site $site, string $lineUserId): bool
    {
        try {
            $lineUser = LineUser::where('site_id', $site->id)
                ->where('line_user_id', $lineUserId)
                ->where('is_linked', true)
                ->firstOrFail();

            DB::transaction(function () use ($lineUser) {
                // LINE認証情報を削除
                $lineUser->authenticateOauth()->delete();
                
                // 連携を解除
                $lineUser->unlinkUser();
            });

            return true;

        } catch (\Exception $e) {
            Log::error('LINE連携解除エラー: ' . $e->getMessage());
            throw new LineMessagingException('LINE連携解除に失敗しました');
        }
    }

    /**
     * ユーザーアカウントとの連携
     */
    public function linkWithUser(LineUser $lineUser, User $user): bool
    {
        try {
            DB::transaction(function () use ($lineUser, $user) {
                // 既存の連携があれば解除
                $existingLink = LineUser::where('site_id', $lineUser->site_id)
                    ->where('user_id', $user->id)
                    ->where('is_linked', true)
                    ->first();

                if ($existingLink) {
                    $existingLink->unlinkUser();
                }

                // 新しい連携を設定
                $lineUser->linkWithUser($user);
            });

            return true;

        } catch (\Exception $e) {
            Log::error('ユーザー連携エラー: ' . $e->getMessage());
            throw new LineMessagingException('ユーザーアカウントとの連携に失敗しました');
        }
    }
}
