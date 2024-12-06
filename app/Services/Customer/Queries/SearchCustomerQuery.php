<?php

namespace App\Services\Customer\Queries;

use App\Models\User;
use App\Services\Customer\DTOs\CustomerData;
use App\Services\Customer\DTOs\CustomerSearchCriteria;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchCustomerQuery
{
    /**
     * 検索条件に基づいて顧客を検索します
     *
     * @param CustomerSearchCriteria $criteria
     * @return LengthAwarePaginator
     */
    public function execute(CustomerSearchCriteria $criteria): LengthAwarePaginator
    {
        $query = User::query();

        // キーワード検索
        if ($criteria->keyword) {
            $query->where(function ($q) use ($criteria) {
                $q->where('name', 'like', "%{$criteria->keyword}%")
                    ->orWhere('email', 'like', "%{$criteria->keyword}%")
                    ->orWhere('phone', 'like', "%{$criteria->keyword}%");
            });
        }

        // メールアドレス検索
        if ($criteria->email) {
            $query->where('email', 'like', "%{$criteria->email}%");
        }

        // 電話番号検索
        if ($criteria->phone) {
            $query->where('phone', 'like', "%{$criteria->phone}%");
        }

        // アクティブ状態による絞り込み
        if ($criteria->isActive !== null) {
            $query->where('is_active', $criteria->isActive);
        }

        // 作成日時による絞り込み
        if ($criteria->createdFrom) {
            $query->where('created_at', '>=', $criteria->createdFrom);
        }
        if ($criteria->createdTo) {
            $query->where('created_at', '<=', $criteria->createdTo);
        }

        // ソート
        $query->orderBy($criteria->sortBy, $criteria->sortOrder);

        // ページネーション
        return $query->paginate($criteria->perPage)
            ->through(fn($user) => CustomerData::fromArray($user->toArray()));
    }

    /**
     * 検索条件に基づいて顧客数を取得します
     *
     * @param CustomerSearchCriteria $criteria
     * @return int
     */
    public function count(CustomerSearchCriteria $criteria): int
    {
        $query = User::query();

        if ($criteria->keyword) {
            $query->where(function ($q) use ($criteria) {
                $q->where('name', 'like', "%{$criteria->keyword}%")
                    ->orWhere('email', 'like', "%{$criteria->keyword}%")
                    ->orWhere('phone', 'like', "%{$criteria->keyword}%");
            });
        }

        if ($criteria->isActive !== null) {
            $query->where('is_active', $criteria->isActive);
        }

        return $query->count();
    }
}
