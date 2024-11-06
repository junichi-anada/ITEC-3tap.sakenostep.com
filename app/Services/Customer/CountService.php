<?php

namespace App\Services\Customer;

use App\Models\Authenticate;
use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CountService
{
    /**
     * ユーザー数を取得
     *
     * @return void
     */
    public function getUserCount()
    {
        $auth = Auth::user();

        $user_count_auth = Authenticate::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->whereNull('deleted_at')
            ->count();

        $user_count_oauth = AuthenticateOauth::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->whereNull('deleted_at')
            ->count();
        
        return $user_count_auth + $user_count_oauth;
    }

    /**
     * 本日の新規ユーザー数を取得
     *
     * @return void
     */
    public function getNewUserCount()
    {
        $auth = Auth::user();
        $auth_user_count = Authenticate::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();

        $oauth_user_count = AuthenticateOauth::where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNull('deleted_at')
            ->count();
        
        return $auth_user_count + $oauth_user_count;
    }

    /**
     * LINEユーザー数を取得
     *
     * @return void
     */
    public function getLineUserCount()
    {
        $auth = Auth::user();
        return AuthenticateOauth::where('auth_provider_id', 1)
            ->where('entity_type', User::class)
            ->where('site_id', $auth->site_id)
            ->whereNull('deleted_at')
            ->count();
    }
}