<?php

namespace App\Services\Operator\Customer\Component\List;

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
     */
    private function applySearchConditions($query, array $search)
    {
        // 検索条件をクエリに適用するロジックをここに追加
        // 例: $query->where('name', 'like', '%' . $search['name'] . '%');

        return $query;
    }
}
