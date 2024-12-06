<?php

namespace App\Services\OrderDetail\Actions;

use App\Models\OrderDetail;
use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\OrderDetail\Traits\OrderDetailServiceTrait;
use App\Repositories\OrderDetail\OrderDetailRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Item\ItemRepository;

class AddOrderDetailAction
{
    use OrderDetailServiceTrait;

    public function __construct(
        private OrderDetailRepository $orderDetailRepository,
        private OrderRepository $orderRepository,
        private ItemRepository $itemRepository
    ) {}

    public function execute(OrderDetailData $data): ?OrderDetail
    {
        return $this->executeWithErrorHandling(function () use ($data) {
            // 商品コードから商品情報を取得
            $item = $this->itemRepository->findByItemCode($data->itemCode);
            if (!$item) {
                throw new \Exception("商品が見つかりません。商品コード: {$data->itemCode}");
            }

            // 未発注の注文を検索
            $order = $this->orderRepository->findUnorderedOrder($data->userId, $data->siteId);

            // 未発注の注文がない場合は新規作成
            if (!$order) {
                $order = $this->orderRepository->create([
                    'user_id' => $data->userId,
                    'site_id' => $data->siteId,
                    'status' => 'draft',
                    'ordered_at' => null
                ]);
            }

            // 注文詳細データを作成
            $orderDetailData = [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'volume' => $data->volume,
                'unit_price' => $item->unit_price,
                'unit_name' => $item->unit->name ?? 'default_unit',
                'price' => $item->unit_price * $data->volume,
                'tax' => $item->unit_price * $data->volume * 0.1,
            ];

            // 既存の注文詳細を検索
            $existingOrderDetail = $this->orderDetailRepository->findBy([
                'order_id' => $order->id,
                'item_id' => $item->id
            ])->first();

            // 既存の注文詳細がある場合は数量を更新
            if ($existingOrderDetail) {
                $newVolume = $existingOrderDetail->volume + $data->volume;
                return $this->orderDetailRepository->update(
                    $existingOrderDetail->id,
                    [
                        'volume' => $newVolume,
                        'price' => $item->unit_price * $newVolume,
                        'tax' => $item->unit_price * $newVolume * 0.1,
                    ]
                );
            }

            // 新規注文詳細を作成
            return $this->orderDetailRepository->create($orderDetailData);
        }, '注文詳細の追加に失敗しました');
    }
}
