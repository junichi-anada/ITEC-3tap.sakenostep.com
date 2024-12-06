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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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
        private readonly DeleteOrderAction $deleteAction
    ) {}

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
        return $this->repository->findByCriteria($criteria);
    }

    /**
     * 注文を取得
     */
    public function find(int $id): ?Order
    {
        return $this->repository->find($id);
    }

    /**
     * ユーザーIDとサイトIDに基づいて最新の未発注の注文を取得
     */
    public function getLatestUnorderedOrderByUserAndSite(int $userId, int $siteId): ?Order
    {
        return $this->repository->findUnorderedOrder($userId, $siteId);
    }

    /**
     * 注文日を更新
     */
    public function updateOrderDate(int $id): bool
    {
        return DB::transaction(fn () => $this->repository->updateOrderDate($id));
    }
}
