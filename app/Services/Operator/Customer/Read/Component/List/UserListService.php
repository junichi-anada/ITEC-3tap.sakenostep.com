<?php

namespace App\Services\Operator\Customer\Component\List;

use App\Models\Authenticate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * ユーザー一覧取得サービスクラス
 *
 * このクラスはユーザー一覧を取得するためのサービスを提供します。
 */
final class UserListService
{
    /**
     * ユーザー一覧を取得
     *
     * @return \Illuminate\Database\Eloquent\Collection ユーザーのコレクション
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
     * @return \Illuminate\Database\Eloquent\Collection ユーザーのコレクション
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
