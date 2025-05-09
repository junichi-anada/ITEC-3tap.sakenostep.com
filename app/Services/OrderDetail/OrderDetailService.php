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

    /**
     * 注文IDと商品IDに基づいて注文詳細の数量、価格、税を更新する。
     * (OrderController@order から呼び出されることを想定)
     *
     * @param int $orderId 注文ID
     * @param int $itemId 商品ID
     * @param int $newVolume 新しい数量
     * @param int $userId ユーザーID (将来的な拡張やログのため)
     * @param int $siteId サイトID (将来的な拡張やログのため)
     * @param \App\Models\Item $item 更新対象の商品モデル (単価情報を含む)
     * @return bool 更新の成否
     */
    public function updateOrAddOrderDetailByItem(
        int $orderId,
        int $itemId,
        int $newVolume,
        int $userId,
        int $siteId,
        \App\Models\Item $item
    ): bool {
        return $this->executeWithErrorHandling(function () use ($orderId, $itemId, $newVolume, $item) {
            $existingOrderDetail = $this->orderDetailRepository->findBy([
                'order_id' => $orderId,
                'item_id' => $itemId,
            ])->first();

            if ($existingOrderDetail) {
                $updateData = [
                    'volume' => $newVolume,
                    'price' => $item->unit_price * $newVolume,
                    'tax' => $item->unit_price * $newVolume * 0.1, // 税率が0.1固定と仮定
                    // unit_price や unit_name は商品マスタに依存するため、ここでは更新しない
                ];
                return $this->orderDetailRepository->update($existingOrderDetail->id, $updateData);
            } else {
                // 基本的には /order/add で OrderDetail は作成されているはずなので、ここに来るケースは例外的。
                // もし、注文確定時にカートになかった商品を追加する仕様であれば、ここで新規作成ロジックが必要。
                // 今回は、既存のものが必ずある前提で、見つからなければエラー(falseを返す)とする。
                \Illuminate\Support\Facades\Log::warning('[OrderDetailService] OrderDetail not found for update in order confirmation.', [
                    'order_id' => $orderId,
                    'item_id' => $itemId,
                    'new_volume' => $newVolume
                ]);
                return false; // または例外をスローする
            }
        }, "注文詳細の数量更新に失敗しました。注文ID: {$orderId}, 商品ID: {$itemId}");
    }
}
