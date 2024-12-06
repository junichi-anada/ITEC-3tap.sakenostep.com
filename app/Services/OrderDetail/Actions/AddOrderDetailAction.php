<?php

namespace App\Services\OrderDetail\Actions;

use App\Models\OrderDetail;
use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\OrderDetail\Traits\OrderDetailServiceTrait;
use App\Repositories\OrderDetail\OrderDetailRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Item\ItemRepository;
use App\Services\Item\ItemService;
use Illuminate\Support\Str;

class AddOrderDetailAction
{
    use OrderDetailServiceTrait;

    public function __construct(
        private OrderDetailRepository $orderDetailRepository,
        private OrderRepository $orderRepository,
        private ItemRepository $itemRepository,
        private ItemService $itemService
    ) {}

    public function execute(OrderDetailData $data): ?OrderDetail
    {
        return $this->executeWithErrorHandling(function () use ($data) {
            // itemCodeからitemを取得
            if ($data->itemCode) {
                $item = $this->itemService->getByCode($data->itemCode, $data->siteId);
                if (!$item) {
                    throw new \Exception("商品が見つかりません。商品コード: {$data->itemCode}");
                }
                $itemId = $item->id;
            } else {
                $itemId = $data->itemId;
            }

            // 商品IDから商品情報を取得
            $item = $this->itemRepository->find($itemId);
            if (!$item) {
                throw new \Exception("商品が見つかりません。商品ID: {$itemId}");
            }

            // サイトIDの確認
            if ($item->site_id !== $data->siteId) {
                throw new \Exception("商品は指定されたサイトに属していません。");
            }

            // 注文IDが指定されている場合はその注文を使用
            if ($data->orderId) {
                $order = $this->orderRepository->find($data->orderId);
                if (!$order) {
                    throw new \Exception("指定された注文が見つかりません。注文ID: {$data->orderId}");
                }
            } else {
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
            }

            // 注文詳細データを作成
            $orderDetailData = [
                'detail_code' => (string) Str::uuid(),
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

            // 既存の注文詳細がある場合は数量を上書き
            if ($existingOrderDetail) {
                $this->orderDetailRepository->update(
                    $existingOrderDetail->id,
                    [
                        'volume' => $data->volume, // 数量を上書き
                        'price' => $item->unit_price * $data->volume,
                        'tax' => $item->unit_price * $data->volume * 0.1,
                    ]
                );
                return $this->orderDetailRepository->find($existingOrderDetail->id);
            }

            // 新規注文詳細を作成
            return $this->orderDetailRepository->create($orderDetailData);
        }, '注文詳細の追加に失敗しました');
    }
}
