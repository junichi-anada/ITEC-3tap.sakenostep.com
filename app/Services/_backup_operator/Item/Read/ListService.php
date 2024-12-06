<?php

namespace App\Services\Operator\Item\Read;

use App\Models\Authenticate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * アイテム一覧取得サービスクラス
 *
 * このクラスはアイテム一覧を取得するためのサービスを提供します。
 */
class ListService
{
    /**
     * ユーザー一覧を取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList()
    {
        $auth = Auth::user();

        return User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $auth->site_id)
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class)
            ->get();
    }

    /**
     * 検索条件を受けてユーザー一覧を取得
     *
     * @param array $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchList(array $search)
    {
        $auth = Auth::user();

        $query = User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $auth->site_id)
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class);

        if (!empty($search['customer_code'])) {
            $query->where('users.user_code', 'like', '%' . $search['customer_code'] . '%');
        }

        if (!empty($search['customer_name'])) {
            $query->where('users.name', 'like', '%' . $search['customer_name'] . '%');
        }

        if (!empty($search['customer_address'])) {
            $query->where('users.address', 'like', '%' . $search['customer_address'] . '%');
        }

        if (!empty($search['customer_phone'])) {
            $query->where('users.phone', 'like', '%' . $search['customer_phone'] . '%');
        }

        if (!empty($search['first_login_date_from'])) {
            $query->where('authenticates.created_at', '>=', $search['first_login_date_from']);
        }

        if (!empty($search['first_login_date_to'])) {
            $query->where('authenticates.created_at', '<=', $search['first_login_date_to']);
        }

        if (!empty($search['last_login_date_from'])) {
            $query->where('authenticates.updated_at', '>=', $search['last_login_date_from']);
        }

        if (!empty($search['last_login_date_to'])) {
            $query->where('authenticates.updated_at', '<=', $search['last_login_date_to']);
        }

        return $query->get();
    }
}
