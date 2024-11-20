<?php
/**
 * 注文詳細の削除サービス
 *
 * 注文詳細から商品を削除するためのサービスクラスです。
 * サイトID、ユーザーID、商品コードを指定して該当する注文詳細をソフトデリートで削除します。
 */
namespace App\Services\OrderDetail;

use App\Models\OrderDetail;
use App\Repositories\OrderDetail\OrderDetailSearchRepository;
use App\Repositories\Order\OrderSearchRepository;
use App\Repositories\Item\ItemSearchRepository;
use Illuminate\Support\Facades\Log;

final class OrderDetailRemoveService
{
    /**
     * @var OrderDetailSearchRepository
     */
    private OrderDetailSearchRepository $orderDetailSearchRepository;

    /**
     * @var OrderSearchRepository
     */
    private OrderSearchRepository $orderSearchRepository;

    /**
     * @var ItemSearchRepository
     */
    private ItemSearchRepository $itemSearchRepository;

    /**
     * コンストラクタ
     *
     * @param OrderDetailSearchRepository $orderDetailSearchRepository
     * @param OrderSearchRepository $orderSearchRepository
     * @param ItemSearchRepository $itemSearchRepository
     */
    public function __construct(
        OrderDetailSearchRepository $orderDetailSearchRepository,
        OrderSearchRepository $orderSearchRepository,
        ItemSearchRepository $itemSearchRepository
    ) {
        $this->orderDetailSearchRepository = $orderDetailSearchRepository;
        $this->orderSearchRepository = $orderSearchRepository;
        $this->itemSearchRepository = $itemSearchRepository;
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

    /**
     * 注文詳細から商品を削除する
     *
     * @param int $siteId サイトID
     * @param int $authId ユーザーID
     * @param string $itemCode 商品コード
     * @return bool|null 削除成功時はtrue、失敗時はnull
     */
    public function remove(int $siteId, int $authId, string $itemCode): ?bool
    {
        return $this->tryCatchWrapper(function () use ($siteId, $authId, $itemCode) {
            // 商品コードから商品情報を取得
            $item = $this->itemSearchRepository->findByItemCode($itemCode);
            if (!$item) {
                Log::error("商品が見つかりません。商品コード: {$itemCode}");
                return false;
            }

            // 未発注の注文を検索
            $order = $this->orderSearchRepository->findUnorderedOrder($authId, $siteId);
            if (!$order) {
                Log::warning("未発注の注文が見つかりません。ユーザーID: {$authId}, サイトID: {$siteId}");
                return false;
            }

            // 注文詳細を検索
            $orderDetail = $this->orderDetailSearchRepository->findByOrderAndItem(
                $order->id,
                $item->id
            );

            if (!$orderDetail) {
                Log::warning("注文詳細が見つかりません。注文ID: {$order->id}, 商品ID: {$item->id}");
                return false;
            }

            return $orderDetail->delete();
        }, '注文詳細の削除に失敗しました');
    }
}

