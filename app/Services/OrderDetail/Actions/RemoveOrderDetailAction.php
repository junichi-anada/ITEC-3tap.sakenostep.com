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
            // 商品コードから商品情報を取得
            $item = $this->itemRepository->findByItemCode($data->itemCode);
            if (!$item) {
                throw new \Exception("商品が見つかりません。商品コード: {$data->itemCode}");
            }

            // 未発注の注文を検索
            $order = $this->orderRepository->findUnorderedOrder($data->userId, $data->siteId);
            if (!$order) {
                throw new \Exception("未発注の注文が見つかりません。ユーザーID: {$data->userId}, サイトID: {$data->siteId}");
            }

            // 注文詳細を検索
            $orderDetail = $this->orderDetailRepository->findBy([
                'order_id' => $order->id,
                'item_id' => $item->id
            ])->first();

            if (!$orderDetail) {
                throw new \Exception("注文詳細が見つかりません。注文ID: {$order->id}, 商品ID: {$item->id}");
            }

            return $this->orderDetailRepository->delete($orderDetail->id);
        }, '注文詳細の削除に失敗しました');
    }
}
