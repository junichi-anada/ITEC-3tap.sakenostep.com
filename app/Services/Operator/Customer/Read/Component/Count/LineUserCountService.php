<?php

namespace App\Services\Operator\Customer\Read\Component\Count;

use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * LINEユーザー数カウントサービスクラス
 *
 * このクラスはLINEユーザー数をカウントするためのサービスを提供します。
 */
final class LineUserCountService
{
    /**
     * LINEユーザー数を取得
     *
     * @return int LINEユーザー数
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
