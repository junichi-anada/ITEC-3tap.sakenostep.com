<?php
/**
 * 注文詳細の一括削除サービス
 *
 * 未発注の注文に登録されている全ての注文詳細を一括で論理削除するためのサービスクラスです。
 * サイトIDとユーザーIDを指定して、該当する未発注の注文の全ての注文詳細をソフトデリートで削除します。
 */
namespace App\Services\OrderDetail;

use App\Repositories\OrderDetail\OrderDetailSearchRepository;
use App\Repositories\Order\OrderSearchRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class OrderDetailAllRemoveService
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
     * コンストラクタ
     *
     * @param OrderDetailSearchRepository $orderDetailSearchRepository
     * @param OrderSearchRepository $orderSearchRepository
     */
    public function __construct(
        OrderDetailSearchRepository $orderDetailSearchRepository,
        OrderSearchRepository $orderSearchRepository
    ) {
        $this->orderDetailSearchRepository = $orderDetailSearchRepository;
        $this->orderSearchRepository = $orderSearchRepository;
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
     * 未発注の注文の全ての注文詳細を削除する
     *
     * @param int $siteId サイトID
     * @param int $authId ユーザーID
     * @return int|null 削除成功時は削除した件数、失敗時はnull
     */
    public function removeAll(int $siteId, int $authId): ?bool
    {
        return $this->tryCatchWrapper(function () use ($siteId, $authId) {
            // 未発注の注文を検索
            $order = $this->orderSearchRepository->findUnorderedOrder($authId, $siteId);
            if (!$order) {
                Log::warning("未発注の注文が見つかりません。ユーザーID: {$authId}, サイトID: {$siteId}");
                return false;
            }

            $result = $this->orderDetailDeleteRepository->deleteByOrderId($order->id);

            return $result;

        }, '注文詳細の一括削除に失敗しました');
    }
}
