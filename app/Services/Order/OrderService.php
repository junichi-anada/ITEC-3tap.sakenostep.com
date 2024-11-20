<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * 伝票サービスクラス
 *
 * 主な仕様:
 * - 伝票の取得、作成、更新、削除などの操作を提供
 * - OrderRepositoryを使用してデータアクセスを行う
 * - ビジネスロジックの実装を担当
 *
 * 制限事項:
 * - データベースアクセスは全てOrderRepositoryを経由
 * - トランザクション制御はRepositoryに委譲
 */
final class OrderService
{
    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;

    /**
     * コンストラクタ
     *
     * @param OrderRepository $orderRepository 伝票リポジトリ
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * 伝票を作成する
     *
     * @param array<string, mixed> $data 伝票データ
     * @return Order 作成された伝票
     * @throws \Exception データ作成に失敗した場合
     */
    public function createOrder(array $data): Order
    {
        try {
            return $this->orderRepository->create($data);
        } catch (\Exception $e) {
            throw new \Exception("伝票の作成に失敗しました。詳細: " . $e->getMessage());
        }
    }

    /**
     * 伝票を取得する
     *
     * @param int $id 伝票ID
     * @return Order|null 伝票
     * @throws \Exception データ取得に失敗した場合
     */
    public function getOrder(int $id): ?Order
    {
        try {
            return $this->orderRepository->find($id);
        } catch (\Exception $e) {
            throw new \Exception("伝票の取得に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * すべての伝票を取得する
     *
     * @return Collection 伝票のコレクション
     * @throws \Exception データ取得に失敗した場合
     */
    public function getAllOrders(): Collection
    {
        try {
            return $this->orderRepository->getAll();
        } catch (\Exception $e) {
            throw new \Exception("伝票一覧の取得に失敗しました。詳細: " . $e->getMessage());
        }
    }

    /**
     * 伝票を更新する
     *
     * @param int $id 伝票ID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws \Exception データ更新に失敗した場合
     */
    public function updateOrder(int $id, array $data): bool
    {
        try {
            return $this->orderRepository->update($id, $data);
        } catch (\Exception $e) {
            throw new \Exception("伝票の更新に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * 伝票を削除する
     *
     * @param int $id 伝票ID
     * @return bool 削除が成功したかどうか
     * @throws \Exception データ削除に失敗した場合
     */
    public function deleteOrder(int $id): bool
    {
        try {
            return $this->orderRepository->delete($id);
        } catch (\Exception $e) {
            throw new \Exception("伝票の削除に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * 未発注の伝票一覧を取得する
     *
     * @return Collection 未発注の伝票のコレクション
     * @throws \Exception データ取得に失敗した場合
     */
    public function getUnorderedOrders(): Collection
    {
        try {
            $conditions = [
                'ordered_at' => null
            ];
            return $this->orderRepository->findBy($conditions);
        } catch (\Exception $e) {
            throw new \Exception("未発注の伝票一覧の取得に失敗しました。詳細: " . $e->getMessage());
        }
    }

    /**
     * 発注済みの伝票一覧を取得する
     *
     * 削除されていない、発注済みの伝票を全て取得します。
     * 取得した伝票は作成日時の降順でソートされます。
     *
     * @param array<string, mixed> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件（デフォルトは作成日時の降順）
     * @return Collection 発注済みの伝票のコレクション
     * @throws \Exception データ取得に失敗した場合
     */
    public function getOrderedOrders(
        array $with = [],
        array $orderBy = ['created_at' => 'desc']
    ): Collection {
        try {
            $conditions = [
                'ordered_at' => ['!=', null],
                'deleted_at' => null
            ];
            return $this->orderRepository->findBy($conditions, $with, $orderBy);
        } catch (\Exception $e) {
            throw new \Exception(sprintf(
                "発注済みの伝票一覧の取得に失敗しました。詳細: %s",
                $e->getMessage()
            ));
        }
    }

    /**
     * ユーザーとサイトに紐づく最新の未発注伝票を取得する
     *
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @return Order|null 未発注伝票
     * @throws \Exception データ取得に失敗した場合
     */
    public function getLatestUnorderedOrderByUserAndSite(int $userId, int $siteId): ?Order
    {
        try {
            $conditions = [
                'user_id' => $userId,
                'site_id' => $siteId,
                'ordered_at' => null,
                'deleted_at' => null
            ];
            $orderBy = ['created_at' => 'desc'];

            $orders = $this->orderRepository->findBy($conditions, [], $orderBy);
            return $orders->first();
        } catch (\Exception $e) {
            throw new \Exception("最新の未発注伝票の取得に失敗しました。ユーザーID: {$userId}, サイトID: {$siteId}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * 指定の伝票番号の注文日を更新する
     *
     * @param int $id 伝票ID
     * @return Order|null 注文
     * @throws \Exception データ取得に失敗した場合
     */
    public function updateOrderDate(int $id): bool
    {
        try {
            return $this->orderRepository->updateOrderDate($id);
        } catch (\Exception $e) {
            throw new \Exception("伝票の注文日の更新に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * サイトとユーザーに紐づく発注済み注文リストを取得する
     *
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @return Collection 発注済み注文のコレクション
     * @throws \Exception データ取得に失敗した場合
     */
    public function getOrderedOrdersByUserAndSite(int $userId, int $siteId): Collection
    {
        try {
            $conditions = [
                'user_id' => $userId,
                'site_id' => $siteId,
                'ordered_at' => ['not_null' => true],
                'deleted_at' => null
            ];
            $orderBy = ['ordered_at' => 'desc'];

            return $this->orderRepository->findBy($conditions, [], $orderBy);
        } catch (\Exception $e) {
            throw new \Exception(sprintf(
                "発注済み注文リストの取得に失敗しました。ユーザーID: %d, サイトID: %d, 詳細: %s",
                $userId,
                $siteId,
                $e->getMessage()
            ));
        }
    }

}
