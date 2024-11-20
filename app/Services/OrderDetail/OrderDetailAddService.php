<?php
/**
 * 注文詳細の作成サービス
 *
 * 注文詳細を作成・追加するためのサービスクラスです。
 * 未発注の注文がある場合はそれに追加し、ない場合は新規注文を作成します。
 */
namespace App\Services\OrderDetail;

use App\Models\OrderDetail;
use App\Repositories\OrderDetail\OrderDetailCreateRepository;
use App\Repositories\OrderDetail\OrderDetailSearchRepository;
use App\Repositories\Order\OrderSearchRepository;
use App\Repositories\Order\OrderCreateRepository;
use App\Repositories\Item\ItemSearchRepository;
use Illuminate\Support\Facades\Log;

final class OrderDetailCreateService
{
    private OrderDetailCreateRepository $orderDetailCreateRepository;
    private OrderDetailSearchRepository $orderDetailSearchRepository;
    private OrderSearchRepository $orderSearchRepository;
    private OrderCreateRepository $orderCreateRepository;
    private ItemSearchRepository $itemSearchRepository;

    public function __construct(
        OrderDetailCreateRepository $orderDetailCreateRepository,
        OrderDetailSearchRepository $orderDetailSearchRepository,
        OrderSearchRepository $orderSearchRepository,
        OrderCreateRepository $orderCreateRepository,
        ItemSearchRepository $itemSearchRepository
    ) {
        $this->orderDetailCreateRepository = $orderDetailCreateRepository;
        $this->orderDetailSearchRepository = $orderDetailSearchRepository;
        $this->orderSearchRepository = $orderSearchRepository;
        $this->orderCreateRepository = $orderCreateRepository;
        $this->itemSearchRepository = $itemSearchRepository;
    }

    /**
     * 商品を注文詳細に追加する
     *
     * @param int $userId ユーザーID
     * @param string $itemCode 商品コード
     * @param int $siteId サイトID
     * @param int $volume 数量
     * @return OrderDetail|null 作成された注文詳細、失敗時はnull
     */
    public function add(int $userId, string $itemCode, int $siteId, int $volume): ?OrderDetail
    {
        return $this->tryCatchWrapper(function () use ($userId, $itemCode, $siteId, $volume) {
            // 商品コードから商品情報を取得
            $item = $this->itemSearchRepository->findByItemCode($itemCode);
            if (!$item) {
                Log::error("商品が見つかりません。商品コード: {$itemCode}");
                return null;
            }

            // 未発注の注文を検索
            $order = $this->orderSearchRepository->findUnorderedOrder($userId, $siteId);

            // 未発注の注文がない場合は新規作成
            if (!$order) {
                $order = $this->orderCreateRepository->create([
                    'user_id' => $userId,
                    'site_id' => $siteId,
                    'status' => 'draft',
                    'ordered_at' => null
                ]);
            }

            // 注文詳細データを作成
            $orderDetailData = [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'volume' => $volume,
                'unit_price' => $item->unit_price,
                'unit_name' => $item->unit->name ?? 'default_unit',
                'price' => $item->unit_price * $volume,
                'tax' => $item->unit_price * $volume * 0.1,
            ];

            // 既存の注文詳細を検索
            $existingOrderDetail = $this->orderDetailSearchRepository->findByOrderAndItem(
                $order->id,
                $item->id
            );

            // 既存の注文詳細がある場合は数量を更新
            if ($existingOrderDetail) {
                $newVolume = $existingOrderDetail->volume + $volume;
                return $this->orderDetailCreateRepository->update(
                    $existingOrderDetail->id,
                    [
                        'volume' => $newVolume,
                        'price' => $item->unit_price * $newVolume,
                        'tax' => $item->unit_price * $newVolume * 0.1,
                    ]
                );
            }

            // 新規注文詳細を作成
            return $this->orderDetailCreateRepository->create($orderDetailData);
        }, '注文詳細の追加に失敗しました');
    }

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("Error: $errorMessage - " . $e->getMessage());
            return null;
        }
    }
}
