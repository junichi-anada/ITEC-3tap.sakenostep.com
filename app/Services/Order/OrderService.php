<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Order\DTOs\OrderData;
use App\Services\Order\DTOs\OrderSearchCriteria;
use App\Services\Order\Actions\CreateOrderAction;
use App\Services\Order\Actions\UpdateOrderAction;
use App\Services\Order\Actions\DeleteOrderAction;
use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\OrderDetail\Actions\AddOrderDetailAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * 注文サービスクラス
 *
 * このクラスは注文に関する全ての操作を一元管理します。
 * 具体的なビジネスロジックはActionsクラスに委譲し、
 * トランザクション管理とリポジトリの操作を担当します。
 */
final class OrderService
{
    public function __construct(
        private readonly OrderRepository $repository,
        private readonly CreateOrderAction $createAction,
        private readonly UpdateOrderAction $updateAction,
        private readonly DeleteOrderAction $deleteAction,
        private readonly AddOrderDetailAction $addOrderDetailAction
    ) {}

    /**
     * 新規注文を作成（コントローラー用の簡易メソッド）
     *
     * @param int $siteId
     * @param int $userId
     * @return Order
     */
    public function createOrder(int $siteId, int $userId): Order
    {
        $orderData = new OrderData(
            siteId: $siteId,
            userId: $userId,
            orderCode: Order::generateOrderCode()
        );

        return $this->create($orderData);
    }

    /**
     * 新規注文を作成
     */
    public function create(OrderData $data): Order
    {
        return DB::transaction(fn () => $this->createAction->execute($data));
    }

    /**
     * 注文を更新
     */
    public function update(int $id, OrderData $data): Order
    {
        return DB::transaction(fn () => $this->updateAction->execute($id, $data));
    }

    /**
     * 注文を削除
     */
    public function delete(int $id): bool
    {
        return DB::transaction(fn () => $this->deleteAction->execute($id));
    }

    /**
     * 注文を検索
     */
    public function search(OrderSearchCriteria $criteria): Collection
    {
        $conditions = $criteria->toArray();
        return $this->repository->findBy(
            conditions: $conditions,
            orderBy: $criteria->orderBy
        );
    }

    /**
     * 注文を取得
     */
    public function find(int $id): ?Order
    {
        return $this->repository->find($id);
    }

    /**
     * 注文コードから注文を取得
     */
    public function getByOrderCode(string $orderCode): ?Order
    {
        $orders = $this->repository->findBy(['order_code' => $orderCode]);
        return $orders->first();
    }

    /**
     * ユーザーIDとサイトIDに基づいて最新の未発注の注文を取得
     */
    public function getLatestUnorderedOrderByUserAndSite(int $userId, int $siteId): ?Order
    {
        return $this->repository->findUnorderedOrder($userId, $siteId);
    }

    /**
     * ユーザーIDとサイトIDに基づいて最新の発注済み注文を取得
     */
    public function getLatestOrderedOrderByUserAndSite(int $userId, int $siteId): ?Order
    {
        return $this->repository->findLatestOrderedOrder($userId, $siteId);
    }

    /**
     * 最新の発注済み注文から新規の未発注注文を作成
     */
    public function createUnorderedOrderFromLatestOrdered(int $userId, int $siteId): ?Order
    {
        return DB::transaction(function () use ($userId, $siteId) {
            $latestOrder = $this->getLatestOrderedOrderByUserAndSite($userId, $siteId);
            if (!$latestOrder) {
                return null;
            }

            // 新規注文を作成
            $orderData = new OrderData(
                siteId: $siteId,
                // ここで $userId (Authenticate ID) ではなく、
                // 認証ユーザーの User モデルの ID を使用する
                // Authenticate モデルに entity_id があると仮定
                userId: \Illuminate\Support\Facades\Auth::user()->entity_id, // <-- ここを修正
                orderCode: Order::generateOrderCode()
            );
            $newOrder = $this->create($orderData);

            // 最新の発注済み注文から商品をコピー
            foreach ($latestOrder->orderDetails as $detail) {
                $orderDetailData = new OrderDetailData(
                    orderId: $newOrder->id,
                    itemId: $detail->item_id,
                    siteId: $siteId,
                    userId: $userId,
                    volume: $detail->volume
                );
                $this->addOrderDetailAction->execute($orderDetailData);
            }

            return $newOrder;
        });
    }

    /**
     * 注文日を更新
     * @return Order|null 更新された注文、失敗時はnull
     */
    public function updateOrderDate(int $id): ?Order
    {
        return $this->repository->updateOrderDate($id);
    }
}
