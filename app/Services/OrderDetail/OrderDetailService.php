<?php

declare(strict_types=1);

namespace App\Services\OrderDetail;

use App\Models\OrderDetail;
use App\Services\OrderDetail\Actions\AddOrderDetailAction;
use App\Services\OrderDetail\Actions\RemoveOrderDetailAction;
use App\Services\OrderDetail\Actions\RemoveAllOrderDetailsAction;
use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\OrderDetail\Traits\OrderDetailServiceTrait;
use App\Repositories\OrderDetail\OrderDetailRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * 注文詳細サービスクラス
 *
 * このクラスは注文詳細に関する操作を提供します。
 * Actionsパターンを採用し、複雑な操作は個別のActionクラスに委譲します。
 */
final class OrderDetailService
{
    use OrderDetailServiceTrait;

    public function __construct(
        private OrderDetailRepository $orderDetailRepository,
        private AddOrderDetailAction $addOrderDetailAction,
        private RemoveOrderDetailAction $removeOrderDetailAction,
        private RemoveAllOrderDetailsAction $removeAllOrderDetailsAction
    ) {}

    /**
     * 注文詳細を追加する
     *
     * @param OrderDetailData $data
     * @return OrderDetail|null
     */
    public function addOrderDetail(OrderDetailData $data): ?OrderDetail
    {
        return $this->addOrderDetailAction->execute($data);
    }

    /**
     * 注文詳細を削除する
     *
     * @param OrderDetailData $data
     * @return bool
     */
    public function removeOrderDetail(OrderDetailData $data): bool
    {
        return $this->removeOrderDetailAction->execute($data);
    }

    /**
     * 注文の全ての注文詳細を削除する
     *
     * @param OrderDetailData $data
     * @return bool
     */
    public function removeAllOrderDetails(OrderDetailData $data): bool
    {
        return $this->removeAllOrderDetailsAction->execute($data);
    }

    /**
     * 注文詳細を取得する
     *
     * @param int $id
     * @return OrderDetail|null
     */
    public function getOrderDetail(int $id): ?OrderDetail
    {
        return $this->executeWithErrorHandling(
            fn () => $this->orderDetailRepository->find($id),
            "注文詳細の取得に失敗しました。ID: {$id}"
        );
    }

    /**
     * 注文IDに紐づく注文詳細リストを取得する
     *
     * @param int $orderId
     * @return Collection
     */
    public function getOrderDetailsByOrderId(int $orderId): Collection
    {
        return $this->executeWithErrorHandling(
            fn () => $this->orderDetailRepository->findBy(['order_id' => $orderId]),
            "注文IDに紐づく注文詳細の取得に失敗しました。注文ID: {$orderId}"
        );
    }

    /**
     * 注文詳細を更新する
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateOrderDetail(int $id, array $data): bool
    {
        return $this->executeWithErrorHandling(
            fn () => $this->orderDetailRepository->update($id, $data),
            "注文詳細の更新に失敗しました。ID: {$id}"
        );
    }
}
