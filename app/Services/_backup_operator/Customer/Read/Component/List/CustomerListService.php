<?php

namespace App\Services\Operator\Customer\Read\Component\List;

use App\Models\Authenticate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * 顧客一覧取得サービスクラス
 *
 * このクラスは顧客一覧を取得するためのサービスを提供します。
 */
final class CustomerListService
{
    /**
     * ユーザー一覧を取得
     *
     * @return \Illuminate\Database\Eloquent\Collection 顧客のコレクション
     */
    public function getList()
    {
        $auth = Auth::user();
        return $this->getUsersBySiteId($auth->site_id);
    }

    /**
     * サイトIDに基づいてユーザーを取得
     *
     * @param int $siteId サイトID
     * @return \Illuminate\Database\Eloquent\Collection 顧客のコレクション
     */
    private function getUsersBySiteId(int $siteId)
    {
        return User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $siteId)
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class)
            ->get();
    }
}
