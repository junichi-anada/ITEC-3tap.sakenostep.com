<?php

namespace App\Services\Operator\Item\Read;

use App\Models\Authenticate;
use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * アイテム数カウントサービスクラス
 *
 * このクラスはアイテム数をカウントするためのサービスを提供します。
 */
class CountService
{
    /**
     * ユーザー数を取得
     *
     * @return int
     */
    public function getUserCount(): int
    {
        $auth = Auth::user();

        $userCountAuth = Authenticate::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->whereNull('deleted_at')
            ->count();

        $userCountOauth = AuthenticateOauth::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->whereNull('deleted_at')
            ->count();

        return $userCountAuth + $userCountOauth;
    }

    /**
     * 本日の新規ユーザー数を取得
     *
     * @return int
     */
    public function getNewUserCount(): int
    {
        $auth = Auth::user();
        $authUserCount = Authenticate::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();

        $oauthUserCount = AuthenticateOauth::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();

        return $authUserCount + $oauthUserCount;
    }

    /**
     * LINEユーザー数を取得
     *
     * @return int
     */
    public function getLineUserCount(): int
    {
        $auth = Auth::user();
        return AuthenticateOauth::where('auth_provider_id', 1)
            ->where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->whereNull('deleted_at')
            ->count();
    }
}
