<?php

namespace App\Services\Order\Actions\OperatorOrderActions;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\Order\DTOs\OrderData;
use App\Services\Order\Exceptions\OrderException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UpdateOrderAction
{
    use OperatorActionTrait;

    /**
     * 注文を更新します
     *
     * @param int $orderId
     * @param OrderData $data
     * @param int $operatorId
     * @return OrderData
     * @throws OrderException
     */
    public function execute(int $orderId, OrderData $data, int $operatorId): OrderData
    {
        if (!$this->hasPermission($operatorId)) {
            throw OrderException::updateFailed($orderId, 'Operator does not have permission');
        }

        $order = Order::find($orderId);
        if (!$order) {
            throw OrderException::notFound($orderId);
        }

        $this->validateData($data, $orderId);
        $this->validateOrderStatus($order, $data->status);

        try {
            DB::beginTransaction();

            // 注文基本情報の更新
            $order->customer_id = $data->customerId;
            $order->total_amount = $data->totalAmount;
            $order->status = $data->status;
            $order->payment_method = $data->paymentMethod;
            $order->shipping_address = $data->shippingAddress;
            if ($data->metadata) {
                $order->metadata = $data->metadata;
            }
            $order->save();

            // 注文詳細の更新
            if ($data->orderDetails) {
                // 既存の注文詳細を削除
                $order->orderDetails()->delete();

                // 新しい注文詳細を作成
                foreach ($data->orderDetails as $detail) {
                    $orderDetail = new OrderDetail();
                    $orderDetail->order_id = $order->id;
                    $orderDetail->item_id = $detail->itemId;
                    $orderDetail->quantity = $detail->quantity;
                    $orderDetail->unit_price = $detail->unitPrice;
                    $orderDetail->save();
                }
            }

            $this->logOperation($operatorId, 'order.update', [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'total_amount' => $order->total_amount,
                'status' => $order->status
            ]);

            DB::commit();

            // 関連データを含めて返却
            return OrderData::fromArray(array_merge(
                $order->toArray(),
                ['order_details' => $order->orderDetails->toArray()]
            ));
        } catch (Throwable $e) {
            DB::rollBack();
            throw OrderException::updateFailed($orderId, $e->getMessage());
        }
    }

    /**
     * データのバリデーションを行います
     *
     * @param OrderData $data
     * @param int $orderId
     * @throws OrderException
     */
    private function validateData(OrderData $data, int $orderId): void
    {
        $validator = Validator::make($data->toArray(), [
            'customer_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'payment_method' => 'required|string',
            'shipping_address' => 'required|array',
            'metadata' => 'nullable|array',
            'order_details' => 'required|array|min:1',
            'order_details.*.item_id' => 'required|exists:items,id',
            'order_details.*.quantity' => 'required|integer|min:1',
            'order_details.*.unit_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            throw OrderException::invalidData($validator->errors()->first());
        }

        // 在庫チェック
        foreach ($data->orderDetails as $detail) {
            $item = DB::table('items')->find($detail->itemId);
            if (!$item) {
                throw OrderException::invalidData("Item not found: {$detail->itemId}");
            }

            // 現在の注文の同じ商品の数量を取得
            $currentQuantity = DB::table('order_details')
                ->where('order_id', $orderId)
                ->where('item_id', $detail->itemId)
                ->value('quantity') ?? 0;

            // 追加で必要な在庫数を計算
            $additionalQuantity = $detail->quantity - $currentQuantity;
            if ($additionalQuantity > 0 && $item->stock < $additionalQuantity) {
                throw OrderException::invalidData("Insufficient stock for item ID: {$detail->itemId}");
            }
        }
    }

    /**
     * 注文ステータスの変更が有効かチェックします
     *
     * @param Order $order
     * @param string $newStatus
     * @throws OrderException
     */
    private function validateOrderStatus(Order $order, string $newStatus): void
    {
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];

        if (!isset($validTransitions[$order->status])) {
            throw OrderException::invalidData("Invalid current status: {$order->status}");
        }

        if ($order->status !== $newStatus && 
            !in_array($newStatus, $validTransitions[$order->status])) {
            throw OrderException::invalidData(
                "Cannot change status from {$order->status} to {$newStatus}"
            );
        }
    }
}
