<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\Order\Dto\OrderUpdateDto;
use App\Services\ServiceErrorHandler;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderUpdateService
{
    use ServiceErrorHandler;

    /**
     * 注文情報を更新
     *
     * @param OrderUpdateDto $dto
     * @return bool
     */
    public function execute(OrderUpdateDto $dto): bool
    {
        try {
            DB::beginTransaction();

            // 注文データを取得
            $order = Order::where('order_number', $dto->getOrderCode())
                         ->with('orderDetails.item')
                         ->firstOrFail();

            // 注文明細の数量を更新
            foreach ($dto->getDetails() as $detail) {
                $orderDetail = $order->orderDetails()
                    ->whereHas('item', function ($query) use ($detail) {
                        $query->where('code', $detail['item_code']);
                    })
                    ->firstOrFail();

                $orderDetail->quantity = $detail['quantity'];
                $orderDetail->save();
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->setError('注文情報の更新に失敗しました。');
            return false;
        }
    }
}
