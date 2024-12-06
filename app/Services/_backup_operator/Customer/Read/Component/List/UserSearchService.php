<?php

namespace App\Services\Operator\Customer\Read\Component\List;

use App\Models\Authenticate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * ユーザー検索サービスクラス
 *
 * このクラスはユーザー検索を行うためのサービスを提供します。
 */
final class UserSearchService
{
    /**
     * 検索条件を受けてユーザー一覧を取得
     *
     * @param array $search 検索条件
     * @return \Illuminate\Database\Eloquent\Collection ユーザーのコレクション
     */
    public function searchList(array $search)
    {
        $auth = Auth::user();
        return $this->getUsersBySiteId($auth->site_id, $search);
    }

    /**
     * サイトIDに基づいてユーザーを取得
     *
     * @param int $siteId サイトID
     * @param array|null $search 検索条件
     * @return \Illuminate\Database\Eloquent\Collection ユーザーのコレクション
     */
    private function getUsersBySiteId(int $siteId, array $search = null)
    {
        $query = User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $siteId)
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class);

        if ($search) {
            $query = $this->applySearchConditions($query, $search);
        }

        return $query->get();
    }

    /**
     * 検索条件をクエリに適用
     *
     * @param \Illuminate\Database\Eloquent\Builder $query クエリビルダー
     * @param array $search 検索条件
     * @return \Illuminate\Database\Eloquent\Builder 更新されたクエリビルダー
     *         $search = [
     *             'customer_code' => $request->customer_code,
     *             'customer_name' => $request->customer_name,
     *             'customer_address' => $request->customer_address,
     *             'customer_phone' => $request->customer_phone,
     *             'first_login_date_from' => $request->first_login_date_from,
     *             'first_login_date_to' => $request->first_login_date_to,
     *             'last_login_date_from' => $request->last_login_date_from,
     *             'last_login_date_to' => $request->last_login_date_to,
     *         ];
     */
    private function applySearchConditions($query, array $search)
    {

        if (isset($search['customer_code'])) {
            $query->where('authenticates.login_code', 'like', '%' . $search['customer_code'] . '%');
        }

        if (isset($search['customer_name'])) {
            $query->where('users.name', 'like', '%' . $search['customer_name'] . '%');
        }

        if (isset($search['customer_address'])) {
            $query->where('users.address', 'like', '%' . $search['customer_address'] . '%');
        }

        $query->where(function($q) use ($search) {
            if (isset($search['customer_phone'])) {
                $q->where('users.phone', $search['customer_phone']);
            }
            if (isset($search['customer_phone2'])) {
                $q->orWhere('users.phone2', $search['customer_phone2']);
            }
            if (isset($search['customer_fax'])) {
                $q->orWhere('users.fax', $search['customer_fax']);
            }
        });

        if (isset($search['first_login_date_from'])) {
            $query->where('authenticates.created_at', '>=', $search['first_login_date_from']);
        }
        if (isset($search['first_login_date_to'])) {
            $query->where('authenticates.created_at', '<=', $search['first_login_date_to']);
        }
        if (isset($search['last_login_date_from'])) {
            $query->where('authenticates.updated_at', '>=', $search['last_login_date_from']);
        }
        if (isset($search['last_login_date_to'])) {
            $query->where('authenticates.updated_at', '<=', $search['last_login_date_to']);
        }

        return $query;
    }
}
