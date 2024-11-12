<?php

namespace App\Services\Operator\Customer\Read\Component\Count;

use App\Models\Authenticate;
use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * ユーザー数カウントサービスクラス
 *
 * このクラスはユーザー数をカウントするためのサービスを提供します。
 */
final class UserCountService
{
    /**
     * ユーザー数を取得
     *
     * @return int ユーザー数
     */
    public function getUserCount(): int
    {
        $auth = Auth::user();

        $userCountAuth = $this->countUsersByType(Authenticate::class, $auth->site_id);
        $userCountOauth = $this->countUsersByType(AuthenticateOauth::class, $auth->site_id);

        return $userCountAuth + $userCountOauth;
    }

    /**
     * 指定されたタイプのユーザー数をカウントする
     *
     * @param string $type ユーザータイプ
     * @param int $siteId サイトID
     * @return int ユーザー数
     */
    private function countUsersByType(string $type, int $siteId): int
    {
        return $type::where('entity_type', User::class)
            ->where('site_id', $siteId)
            ->whereNull('deleted_at')
            ->count();
    }
}
