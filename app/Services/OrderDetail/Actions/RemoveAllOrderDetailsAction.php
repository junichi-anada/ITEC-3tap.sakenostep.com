<?php

namespace App\Services\OrderDetail\Actions;

use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\OrderDetail\Traits\OrderDetailServiceTrait;
use App\Repositories\OrderDetail\OrderDetailRepository;
use App\Repositories\Order\OrderRepository;

class RemoveAllOrderDetailsAction
{
    use OrderDetailServiceTrait;

    public function __construct(
        private OrderDetailRepository $orderDetailRepository,
        private OrderRepository $orderRepository
    ) {}

    public function execute(OrderDetailData $data): bool
    {
        return $this->executeWithErrorHandling(function () use ($data) {
            // 未発注の注文を検索
            $order = $this->orderRepository->findUnorderedOrder($data->userId, $data->siteId);
            if (!$order) {
                throw new \Exception("未発注の注文が見つかりません。ユーザーID: {$data->userId}, サイトID: {$data->siteId}");
            }

            // 注文に紐づく全ての注文詳細を削除
            $result = $this->orderDetailRepository->deleteAllByOrderId($order->id);
            
            if (!$result) {
                throw new \Exception("注文詳細の一括削除に失敗しました。注文ID: {$order->id}");
            }

            return $result;
        }, '注文詳細の一括削除に失敗しました');
    }
}
