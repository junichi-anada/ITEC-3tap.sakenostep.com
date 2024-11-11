<?php
/**
 * 注文詳細の作成サービス
 */
namespace App\Services\OrderDetail\Customer;

use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateService
{
    /**
     * 新しい注文詳細を作成する
     *
     * @param int $orderId
     * @param \App\Models\Item $item
     * @param int $volume
     * @return OrderDetail|null
     */
    public function create(int $orderId, $item, int $volume): ?OrderDetail
    {
        Log::info('item: ' . json_encode($item));
        Log::info('volume: ' . $volume);
        Log::info('orderId: ' . $orderId);

        $data = [
            'order_id' => $orderId,
            'item_id' => $item->id,
            'volume' => $volume,
            'unit_price' => $item->unit_price,
            'unit_name' => $item->unit->name ?? 'default_unit',
            'price' => $item->unit_price * $volume,
            'tax' => $item->unit_price * $volume * 0.1,
        ];

        // detail_code を生成
        do {
            $data['detail_code'] = Str::ulid();
        } while (OrderDetail::where('detail_code', $data['detail_code'])->exists());

        return $this->tryCatchWrapper(function () use ($data) {
            return OrderDetail::create($data);
        }, '注文詳細の作成に失敗しました');
    }

    /**
     * OrderDetailオブジェクトから新しい注文詳細を作成する
     *
     * @param int $orderId
     * @param \App\Models\OrderDetail $orderDetail
     * @return OrderDetail|null
     */
    public function createFromOrderDetail(int $orderId, OrderDetail $orderDetail): ?OrderDetail
    {
        $data = [
            'order_id' => $orderId,
            'item_id' => $orderDetail->item_id,
            'volume' => $orderDetail->volume,
            'unit_price' => $orderDetail->unit_price,
            'unit_name' => $orderDetail->unit_name,
            'price' => $orderDetail->price,
            'tax' => $orderDetail->tax,
        ];

        // detail_code を生成
        do {
            $data['detail_code'] = Str::ulid();
        } while (OrderDetail::where('detail_code', $data['detail_code'])->exists());

        return $this->tryCatchWrapper(function () use ($data) {
            return OrderDetail::create($data);
        }, '注文詳細の作成に失敗しました');
    }

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("Error: $errorMessage - " . $e->getMessage());
            return null;
        }
    }
}
