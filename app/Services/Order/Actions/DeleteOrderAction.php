<?php

declare(strict_types=1);

namespace App\Services\Order\Actions;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Order\Exceptions\OrderException;

/**
 * 注文削除アクション
 */
final class DeleteOrderAction
{
    public function __construct(
        private readonly OrderRepository $repository
    ) {}

    /**
     * 注文を削除
     *
     * @throws OrderException 注文の削除に失敗した場合
     */
    public function execute(int $id): bool
    {
        try {
            // 注文の存在確認
            $order = $this->repository->find($id);
            if (!$order) {
                throw new OrderException("ID: {$id} の注文が見つかりません。");
            }

            // 注文が削除可能か確認
            if (!$this->canDelete($order)) {
                throw new OrderException("この注文は削除できません。");
            }

            // 注文の削除
            return $this->repository->delete($id);
        } catch (OrderException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new OrderException(
                '注文の削除に失敗しました: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * 注文が削除可能か確認
     */
    private function canDelete(Order $order): bool
    {
        // 注文済みの場合は削除不可
        if ($order->ordered_at !== null) {
            return false;
        }

        // その他の削除可否条件をここに追加

        return true;
    }
}
