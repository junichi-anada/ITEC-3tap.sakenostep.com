<?php

declare(strict_types=1);

namespace App\Services\Order\Actions;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Order\DTOs\OrderData;
use App\Services\Order\Exceptions\OrderException;
use Illuminate\Support\Str;

/**
 * 注文作成アクション
 */
final class CreateOrderAction
{
    public function __construct(
        private readonly OrderRepository $repository
    ) {}

    /**
     * 注文を作成
     *
     * @throws OrderException 注文の作成に失敗した場合
     */
    public function execute(OrderData $data): Order
    {
        try {
            // 注文コードの生成
            $orderCode = $data->orderCode ?? $this->generateOrderCode();

            // 基本注文データの作成
            $orderData = array_merge($data->toArray(), [
                'order_code' => $orderCode
            ]);

            // 注文の保存
            $order = $this->repository->create($orderData);

            // 注文詳細の作成（必要に応じて）
            if (!empty($data->items)) {
                $this->createOrderDetails($order, $data->items);
            }

            return $order;
        } catch (\Exception $e) {
            throw new OrderException(
                '注文の作成に失敗しました: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * 一意の注文コードを生成
     */
    private function generateOrderCode(): string
    {
        do {
            $orderCode = Str::ulid();
        } while ($this->repository->exists(['order_code' => $orderCode]));

        return $orderCode;
    }

    /**
     * 注文詳細を作成
     */
    private function createOrderDetails(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->details()->create([
                'item_id' => $item['item_id'],
                'volume' => $item['volume'],
                // その他の必要なフィールド
            ]);
        }
    }
}