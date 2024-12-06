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

class CreateOrderAction
{
    use OperatorActionTrait;

    /**
     * 注文を作成します
     *
     * @param OrderData $data
     * @param int $operatorId
     * @return OrderData
     * @throws OrderException
     */
    public function execute(OrderData $data, int $operatorId): OrderData
    {
        if (!$this->hasPermission($operatorId)) {
            throw OrderException::createFailed('Operator does not have permission');
        }

        $this->validateData($data);

        try {
            DB::beginTransaction();

            // 注文の作成
            $order = new Order();
            $order->customer_id = $data->customerId;
            $order->total_amount = $data->totalAmount;
            $order->status = $data->status;
            $order->payment_method = $data->paymentMethod;
            $order->shipping_address = $data->shippingAddress;
            $order->metadata = $data->metadata;
            $order->save();

            // 注文詳細の作成
            foreach ($data->orderDetails as $detail) {
                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->id;
                $orderDetail->item_id = $detail->itemId;
                $orderDetail->quantity = $detail->quantity;
                $orderDetail->unit_price = $detail->unitPrice;
                $orderDetail->save();
            }

            $this->logOperation($operatorId, 'order.create', [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'total_amount' => $order->total_amount
            ]);

            DB::commit();

            // 関連データを含めて返却
            return OrderData::fromArray(array_merge(
                $order->toArray(),
                ['order_details' => $order->orderDetails->toArray()]
            ));
        } catch (Throwable $e) {
            DB::rollBack();
            throw OrderException::createFailed($e->getMessage());
        }
    }

    /**
     * データのバリデーションを行います
     *
     * @param OrderData $data
     * @throws OrderException
     */
    private function validateData(OrderData $data): void
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
            if (!$item || $item->stock < $detail->quantity) {
                throw OrderException::invalidData("Insufficient stock for item ID: {$detail->itemId}");
            }
        }
    }
}
