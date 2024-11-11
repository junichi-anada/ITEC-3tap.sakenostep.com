<?php

namespace App\Services\Operator\Customer;

use App\Models\Authenticate;
use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ListService
{
    /**
     * ユーザー一覧を取得
     *
     * @return void
     */
    public function getList()
    {
        $auth = Auth::user();

        $customers = User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $auth->site_id)
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class);

        $customers = $customers->get();
        
        return $customers;
    }

    /**
     * 検索条件を受けてユーザー一覧を取得
     *
     * @return void
     */
    public function searchList($search)
    {
        $auth = Auth::user();

        $customers = User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $auth->site_id)
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class);

        if (!empty($search['customer_code'])) {
            $customers = $customers->where('users.user_code', 'like', '%' . $search['customer_code'] . '%');
        }

        if (!empty($search['customer_name'])) {
            $customers = $customers->where('users.name', 'like', '%' . $search['customer_name'] . '%');
        }

        if (!empty($search['customer_address'])) {
            $customers = $customers->where('users.address', 'like', '%' . $search['customer_address'] . '%');
        }

        if (!empty($search['customer_phone'])) {
            $customers = $customers->where('users.phone', 'like', '%' . $search['customer_phone'] . '%');
        }

        if (!empty($search['first_login_date_from'])) {
            $customers = $customers->where('authenticates.created_at', '>=', $search['first_login_date_from']);
        }

        if (!empty($search['first_login_date_to'])) {
            $customers = $customers->where('authenticates.created_at', '<=', $search['first_login_date_to']);
        }

        if (!empty($search['last_login_date_from'])) {
            $customers = $customers->where('authenticates.updated_at', '>=', $search['last_login_date_from']);
        }

        if (!empty($search['last_login_date_to'])) {
            $customers = $customers->where('authenticates.updated_at', '<=', $search['last_login_date_to']);
        }

        $customers = $customers->get();
        
        return $customers;
    }
}