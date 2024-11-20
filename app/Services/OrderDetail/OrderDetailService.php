<?php

declare(strict_types=1);

namespace App\Services\OrderDetail;

use App\Models\OrderDetail;
use App\Repositories\OrderDetail\OrderDetailRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * 注文詳細サービスクラス
 *
 * 主な仕様:
 * - 注文詳細の取得、作成、更新、削除などの操作を提供
 * - OrderDetailRepositoryを使用してデータアクセスを行う
 * - ビジネスロジックの実装を担当
 *
 * 制限事項:
 * - データベースアクセスは全てOrderDetailRepositoryを経由
 * - トランザクション制御はRepositoryに委譲
 */
final class OrderDetailService
{
    /**
     * @var OrderDetailRepository
     */
    private OrderDetailRepository $orderDetailRepository;

    /**
     * コンストラクタ
     *
     * @param OrderDetailRepository $orderDetailRepository 注文詳細リポジトリ
     */
    public function __construct(OrderDetailRepository $orderDetailRepository)
    {
        $this->orderDetailRepository = $orderDetailRepository;
    }

    /**
     * 注文詳細を作成する
     *
     * @param array<string, mixed> $data 注文詳細データ
     * @return OrderDetail 作成された注文詳細
     * @throws \Exception データ作成に失敗した場合
     */
    public function createOrderDetail(array $data): OrderDetail
    {
        try {
            return $this->orderDetailRepository->create($data);
        } catch (\Exception $e) {
            throw new \Exception("注文詳細の作成に失敗しました。詳細: " . $e->getMessage());
        }
    }

    /**
     * 注文詳細を取得する
     *
     * @param int $id 注文詳細ID
     * @return OrderDetail|null 注文詳細
     * @throws \Exception データ取得に失敗した場合
     */
    public function getOrderDetail(int $id): ?OrderDetail
    {
        try {
            return $this->orderDetailRepository->find($id);
        } catch (\Exception $e) {
            throw new \Exception("注文詳細の取得に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * すべての注文詳細を取得する
     *
     * @return Collection 注文詳細のコレクション
     * @throws \Exception データ取得に失敗した場合
     */
    public function getAllOrderDetails(): Collection
    {
        try {
            return $this->orderDetailRepository->getAll();
        } catch (\Exception $e) {
            throw new \Exception("注文詳細一覧の取得に失敗しました。詳細: " . $e->getMessage());
        }
    }

    /**
     * 注文詳細を更新する
     *
     * @param int $id 注文詳細ID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws \Exception データ更新に失敗した場合
     */
    public function updateOrderDetail(int $id, array $data): bool
    {
        try {
            return $this->orderDetailRepository->update($id, $data);
        } catch (\Exception $e) {
            throw new \Exception("注文詳細の更新に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * 注文詳細を削除する
     *
     * @param int $id 注文詳細ID
     * @return bool 削除が成功したかどうか
     * @throws \Exception データ削除に失敗した場合
     */
    public function deleteOrderDetail(int $id): bool
    {
        try {
            return $this->orderDetailRepository->delete($id);
        } catch (\Exception $e) {
            throw new \Exception("注文詳細の削除に失敗しました。ID: {$id}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * 伝票番号に紐づく注文詳細リストを取得する
     *
     * @param int $orderId 伝票番号
     * @return Collection 注文詳細のコレクション
     * @throws \Exception データ取得に失敗した場合
     */
    public function getOrderDetailsByOrderId(int $orderId): Collection
    {
        try {
            $conditions = [
                'order_id' => $orderId,
                'deleted_at' => null
            ];
            return $this->orderDetailRepository->findBy($conditions);
        } catch (\Exception $e) {
            throw new \Exception("伝票番号に紐づく注文詳細の取得に失敗しました。伝票番号: {$orderId}, 詳細: " . $e->getMessage());
        }
    }

    /**
     * 伝票から商品を削除する
     *
     * 指定された伝票番号と商品IDに紐づく商品を削除します。
     * 削除に成功した場合は削除された商品モデルを、失敗した場合はnullを返します。
     *
     * @param int $orderId 伝票番号
     * @param int $itemId 商品ID
     * @return bool 削除が成功したかどうか
     * @throws \Exception 商品の削除に失敗した場合
     */
    public function deleteItemFromOrder(int $orderId, int $itemId): bool
    {
        return $this->orderDetailRepository->deleteItemFromOrder($orderId, $itemId);
    }

    /**
     * 伝票番号に紐づく注文詳細を全て削除する
     *
     * 指定された伝票番号に紐づく全ての注文詳細を削除します。
     * 削除に成功した場合はtrue、失敗した場合は例外をスローします。
     *
     * @param int $orderId 伝票番号
     * @return bool 削除が成功したかどうか
     * @throws \Exception 注文詳細の削除に失敗した場合
     */
    public function deleteAllOrderDetails(int $orderId): bool
    {
        try {
            return $this->orderDetailRepository->deleteAllByOrderId($orderId);
        } catch (\Exception $e) {
            throw new \Exception("注文詳細の一括削除に失敗しました。注文ID: {$orderId}, 詳細: " . $e->getMessage());
        }
    }

}
