<?php

namespace App\Services\Order\Queries\OperatorOrderQueries;

use App\Models\Order;
use App\Services\Order\DTOs\OrderData;
use App\Services\Order\DTOs\OrderSearchCriteria;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchOrderQuery
{
    use OperatorActionTrait;

    /**
     * 検索条件に基づいて注文を検索します
     *
     * @param OrderSearchCriteria $criteria
     * @param int $operatorId
     * @return LengthAwarePaginator
     */
    public function execute(OrderSearchCriteria $criteria, int $operatorId): LengthAwarePaginator
    {
        $query = Order::query()
            ->with(['customer', 'orderDetails.item']); // リレーションを事前読み込み

        // キーワード検索（顧客名、メールアドレス）
        if ($criteria->keyword) {
            $query->whereHas('customer', function ($q) use ($criteria) {
                $q->where('name', 'like', "%{$criteria->keyword}%")
                    ->orWhere('email', 'like', "%{$criteria->keyword}%");
            });
        }

        // 注文番号による検索
        if ($criteria->orderId) {
            $query->where('id', $criteria->orderId);
        }

        // 顧客IDによる絞り込み
        if ($criteria->customerId) {
            $query->where('customer_id', $criteria->customerId);
        }

        // ステータスによる絞り込み
        if ($criteria->status) {
            $query->where('status', $criteria->status);
        }

        // 金額範囲による絞り込み
        if ($criteria->minAmount !== null) {
            $query->where('total_amount', '>=', $criteria->minAmount);
        }
        if ($criteria->maxAmount !== null) {
            $query->where('total_amount', '<=', $criteria->maxAmount);
        }

        // 日付範囲による絞り込み
        if ($criteria->dateFrom) {
            $query->where('created_at', '>=', $criteria->dateFrom);
        }
        if ($criteria->dateTo) {
            $query->where('created_at', '<=', $criteria->dateTo);
        }

        // 商品IDによる絞り込み
        if ($criteria->itemId) {
            $query->whereHas('orderDetails', function ($q) use ($criteria) {
                $q->where('item_id', $criteria->itemId);
            });
        }

        // ソート
        $query->orderBy($criteria->sortBy, $criteria->sortOrder);

        // 操作ログを記録
        $this->logOperation($operatorId, 'order.search', [
            'criteria' => $criteria->toArray()
        ]);

        // ページネーション
        return $query->paginate($criteria->perPage)
            ->through(function ($order) {
                return OrderData::fromArray(array_merge(
                    $order->toArray(),
                    [
                        'customer_name' => $order->customer->name ?? null,
                        'customer_email' => $order->customer->email ?? null,
                        'order_details' => $order->orderDetails->map(function ($detail) {
                            return array_merge($detail->toArray(), [
                                'item_name' => $detail->item->name ?? null
                            ]);
                        })->toArray()
                    ]
                ));
            });
    }

    /**
     * 検索条件に基づいて注文数を取得します
     *
     * @param OrderSearchCriteria $criteria
     * @return int
     */
    public function count(OrderSearchCriteria $criteria): int
    {
        $query = Order::query();

        if ($criteria->status) {
            $query->where('status', $criteria->status);
        }

        if ($criteria->dateFrom) {
            $query->where('created_at', '>=', $criteria->dateFrom);
        }
        if ($criteria->dateTo) {
            $query->where('created_at', '<=', $criteria->dateTo);
        }

        return $query->count();
    }

    /**
     * ステータスごとの注文数を取得します
     *
     * @return array
     */
    public function countByStatus(): array
    {
        return DB::table('orders')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * 日別の注文統計を取得します
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getDailyStats(string $startDate, string $endDate): array
    {
        return DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count'),
                DB::raw('sum(total_amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
