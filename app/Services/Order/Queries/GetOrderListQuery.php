<?php

namespace App\Services\Order\Queries;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class GetOrderListQuery
{
    /**
     * 注文一覧を取得
     *
     * @param array $params 検索条件
     * @param bool $paginate ページネーションを行うかどうか
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function execute(array $params, bool $paginate = true)
    {
        $query = Order::with([
            'customer',
            'customer.authenticates',
            'orderDetails.item'
        ])->whereNotNull('ordered_at');  // 注文日がnullのデータを除外

        // 注文番号
        if (!empty($params['order_number'])) {
            $query->where('order_code', 'LIKE', "%{$params['order_number']}%");
        }

        // 顧客名
        if (!empty($params['customer_name'])) {
            $query->whereHas('customer', function (Builder $query) use ($params) {
                $query->where('name', 'LIKE', "%{$params['customer_name']}%");
            });
        }

        // 電話番号
        if (!empty($params['phone'])) {
            $query->whereHas('customer', function (Builder $query) use ($params) {
                $query->where('phone', 'LIKE', "%{$params['phone']}%");
            });
        }

        // 注文日（開始）
        if (!empty($params['order_date_from'])) {
            $query->whereDate('ordered_at', '>=', $params['order_date_from']);
        }

        // 注文日（終了）
        if (!empty($params['order_date_to'])) {
            $query->whereDate('ordered_at', '<=', $params['order_date_to']);
        }

        // CSV書出状態
        if (!empty($params['csv_export_status'])) {
            if ($params['csv_export_status'] === 'exported') {
                $query->whereNotNull('exported_at');
            } elseif ($params['csv_export_status'] === 'not_exported') {
                $query->whereNull('exported_at');
            }
        }

        // 注文日の降順、IDの降順で並び替え
        $query->orderBy('ordered_at', 'desc')
              ->orderBy('id', 'desc');

        return $paginate ? $query->paginate(20) : $query->get();
    }
}
