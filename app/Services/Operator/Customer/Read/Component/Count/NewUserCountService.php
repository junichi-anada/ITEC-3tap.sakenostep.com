<?php

namespace App\Services\Operator\Customer\Read\Component\Count;

use App\Models\Authenticate;
use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * 新規ユーザー数カウントサービスクラス
 *
 * このクラスは新規ユーザー数をカウントするためのサービスを提供します。
 */
final class NewUserCountService
{
    /**
     * 本日の新規ユーザー数を取得
     *
     * @return int 新規ユーザー数
     */
    public function getNewUserCount(): int
    {
        $auth = Auth::user();

        $authUserCount = $this->countNewUsersByType(Authenticate::class, $auth->site_id);
        $oauthUserCount = $this->countNewUsersByType(AuthenticateOauth::class, $auth->site_id);

        return $authUserCount + $oauthUserCount;
    }

    /**
     * 指定されたタイプの本日の新規ユーザー数をカウントする
     *
     * @param string $type ユーザータイプ
     * @param int $siteId サイトID
     * @return int 新規ユーザー数
     */
    private function countNewUsersByType(string $type, int $siteId): int
    {
        return $type::where('entity_type', User::class)
            ->where('site_id', $siteId)
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();
    }
}
