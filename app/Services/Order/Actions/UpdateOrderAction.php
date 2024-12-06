<?php

declare(strict_types=1);

namespace App\Services\Order\Actions;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Order\DTOs\OrderData;
use App\Services\Order\Exceptions\OrderException;

/**
 * 注文更新アクション
 */
final class UpdateOrderAction
{
    public function __construct(
        private readonly OrderRepository $repository
    ) {}

    /**
     * 注文を更新
     *
     * @throws OrderException 注文の更新に失敗した場合
     */
    public function execute(int $id, OrderData $data): Order
    {
        try {
            // 注文の存在確認
            $order = $this->repository->find($id);
            if (!$order) {
                throw new OrderException("ID: {$id} の注文が見つかりません。");
            }

            // 注文が更新可能か確認
            if (!$this->canUpdate($order)) {
                throw new OrderException("この注文は更新できません。");
            }

            // 注文の更新
            $this->repository->update($id, $data->toArray());

            // 注文詳細の更新（必要に応じて）
            if (!empty($data->items)) {
                $this->updateOrderDetails($order, $data->items);
            }

            return $this->repository->find($id);
        } catch (OrderException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new OrderException(
                '注文の更新に失敗しました: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * 注文が更新可能か確認
     */
    private function canUpdate(Order $order): bool
    {
        // 注文済みの場合は更新不可
        if ($order->ordered_at !== null) {
            return false;
        }

        // その他の更新可否条件をここに追加

        return true;
    }

    /**
     * 注文詳細を更新
     */
    private function updateOrderDetails(Order $order, array $items): void
    {
        // 既存の注文詳細を一旦削除
        $order->details()->delete();

        // 新しい注文詳細を作成
        foreach ($items as $item) {
            $order->details()->create([
                'item_id' => $item['item_id'],
                'volume' => $item['volume'],
                // その他の必要なフィールド
            ]);
        }
    }
}
