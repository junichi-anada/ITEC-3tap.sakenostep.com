<?php

namespace App\Services\OrderDetail\Actions;

use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\OrderDetail\Traits\OrderDetailServiceTrait;
use App\Repositories\OrderDetail\OrderDetailRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Item\ItemRepository;

class RemoveOrderDetailAction
{
    use OrderDetailServiceTrait;

    public function __construct(
        private OrderDetailRepository $orderDetailRepository,
        private OrderRepository $orderRepository,
        private ItemRepository $itemRepository
    ) {}

    public function execute(OrderDetailData $data): bool
    {
        return $this->executeWithErrorHandling(function () use ($data) {
            if (!$data->orderId || !$data->itemId) {
                throw new \Exception("注文IDと商品IDは必須です。");
            }

            // 注文詳細を検索
            $orderDetail = $this->orderDetailRepository->findBy([
                'order_id' => $data->orderId,
                'item_id' => $data->itemId
            ])->first();

            if (!$orderDetail) {
                throw new \Exception("注文詳細が見つかりません。注文ID: {$data->orderId}, 商品ID: {$data->itemId}");
            }

            return $this->orderDetailRepository->delete($orderDetail->id);
        }, '注文詳細の削除に失敗しました');
    }
}
