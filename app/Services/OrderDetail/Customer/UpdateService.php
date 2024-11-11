<?php
/**
 * 注文詳細の更新サービス
 */
namespace App\Services\OrderDetail\Customer;

use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;

class UpdateService
{
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

    /**
     * 注文詳細を更新する
     *
     * @param int $orderDetailId
     * @param array $data
     * @return bool|null
     */
    public function update(int $orderDetailId, array $data)
    {
        Log::info("Updating order detail with ID: $orderDetailId");
        return $this->tryCatchWrapper(function () use ($orderDetailId, $data) {
            $orderDetail = OrderDetail::find($orderDetailId);

            if ($orderDetail) {
                return $orderDetail->update($data);
            }

            Log::error("Order detail with ID $orderDetailId not found.");
            return false;
        }, '注文詳細の更新に失敗しました');
    }
}
