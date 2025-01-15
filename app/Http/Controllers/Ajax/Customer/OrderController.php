<?php
/**
 * 顧客向け注文管理機能 Api用コントローラー
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Ajax\BaseAjaxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\Order\OrderService;
use App\Services\OrderDetail\OrderDetailService;
use App\Services\Item\ItemService;
use App\Services\OrderDetail\DTOs\OrderDetailData;
use App\Services\Messaging\LineMessagingService;

class OrderController extends BaseAjaxController
{
    const SUCCESS_MESSAGE = '注文リストに追加しました';
    const DELETE_SUCCESS_MESSAGE = '注文リストから削除しました';
    const DELETE_ALL_SUCCESS_MESSAGE = '注文リストから全て削除しました';
    const ORDER_SUCCESS_MESSAGE = '注文しました';
    const VALIDATION_ERROR_MESSAGE = 'バリデーションエラー';
    const UNEXPECTED_ERROR_MESSAGE = '予期しないエラーが発生しました';
    const INVALID_DETAIL_CODE_MESSAGE = '無効な注文詳細コードです';
    const NOT_FOUND_MESSAGE = '注文詳細データが見つかりません';

    private OrderDetailService $orderDetailService;
    private OrderService $orderService;
    private ItemService $itemService;
    private LineMessagingService $lineMessagingService;

    public function __construct(
        OrderDetailService $orderDetailService,
        OrderService $orderService,
        ItemService $itemService,
        LineMessagingService $lineMessagingService
    ) {
        $this->orderDetailService = $orderDetailService;
        $this->orderService = $orderService;
        $this->itemService = $itemService;
        $this->lineMessagingService = $lineMessagingService;
    }

    /**
     * 注文リストへの登録
     * 注文リストに商品を追加する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|exists:items,item_code',
            'volume' => 'required|integer|min:1',
        ]);

        $auth = $this->getAuthenticatedUser();
        $volume = $request->input('volume', 1);
        $itemCode = $request->input('item_code');

        try {
            $orderDetailData = new OrderDetailData(
                userId: $auth->id,
                siteId: $auth->site_id,
                itemCode: $itemCode,
                volume: $volume
            );

            $orderDetail = $this->orderDetailService->addOrderDetail($orderDetailData);

            // LINE通知の送信
            if ($auth->line_user_id) {
                try {
                    // メッセージテンプレートの作成
                    $message = "ご注文ありがとうございます。\n"
                        . "注文番号：{$orderDetail->order_code}\n"
                        . "合計金額：" . number_format($orderDetail->total_price) . "円\n"
                        . "\n注文の詳細はこちらから確認できます。\n"
                        . url("/customer/order/{$orderDetail->order_code}");

                    // LineMessagingServiceを使用してメッセージを送信
                    $result = $this->lineMessagingService->pushMessage($auth->line_user_id, $message);
                    
                    if ($result) {
                        Log::info('LINE通知送信成功', [
                            'user_id' => $auth->id,
                            'line_user_id' => $auth->line_user_id,
                            'order_code' => $orderDetail->order_code
                        ]);
                    } else {
                        throw new \Exception('LINE通知の送信に失敗しました');
                    }

                } catch (\Exception $e) {
                    // LINE送信失敗のログを記録するが、注文処理は継続
                    Log::error('LINE通知の送信に失敗しました', [
                        'user_id' => $auth->id,
                        'line_user_id' => $auth->line_user_id,
                        'order_code' => $orderDetail->order_code,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return $this->jsonResponse(
                self::SUCCESS_MESSAGE, 
                ['detail_code' => $orderDetail->detail_code]
            );

        } catch (\Exception $e) {
            Log::error('注文処理エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return $this->jsonResponse(
                self::UNEXPECTED_ERROR_MESSAGE, 
                ['error' => $e->getMessage()], 
                500
            );
        }
    }

    /**
     * 注文詳細の指定削除
     * パラメータのItemCodeに一致する商品の注文を削除する。
     *
     * @param string $detailCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $item_code)
    {
        $auth = $this->getAuthenticatedUser();

        try {
            // 削除対象の商品情報を取得
            $item = $this->itemService->getByCode($item_code, $auth->site_id);
            if (!$item) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            // 未発注の注文を検索
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if (!$order) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $orderDetailData = new OrderDetailData(
                userId: $auth->id,
                siteId: $auth->site_id,
                orderId: $order->id,
                itemId: $item->id
            );

            if (!$this->orderDetailService->removeOrderDetail($orderDetailData)) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            return $this->jsonResponse(self::DELETE_SUCCESS_MESSAGE);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 注文リストに登録されている商品の全削除
     * 注文リストに登録されている商品を全て削除する。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll()
    {
        $auth = $this->getAuthenticatedUser();

        try {
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if (!$order) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $orderDetailData = new OrderDetailData(
                userId: $auth->id,
                siteId: $auth->site_id,
                orderId: $order->id
            );

            if (!$this->orderDetailService->removeAllOrderDetails($orderDetailData)) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            return $this->jsonResponse(self::DELETE_ALL_SUCCESS_MESSAGE);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 注文実行
     * 注文リストに登録されている商品を発注する。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order()
    {
        $auth = $this->getAuthenticatedUser();
        
        // デバッグログを追加
        Log::debug('User authentication info', [
            'user_id' => $auth->id,
            'line_user_id' => $auth->line_user_id ?? null,
            'has_line' => !empty($auth->line_user_id)
        ]);

        try {
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if (!$order) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $updatedOrder = $this->orderService->updateOrderDate($order->id);
            if (!$updatedOrder) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            // LINE連携している場合はLINE通知を送信
            if ($auth->line_user_id) {
                try {
                    // メッセージテンプレートの作成
                    $message = "ご注文ありがとうございます。\n"
                        . "注文番号：{$updatedOrder->order_code}\n"
                        // . "合計金額：" . number_format($updatedOrder->total_price) . "円\n"
                        . "\n注文の詳細はこちらから確認できます。\n"
                        . url("/order");
                        // . url("/order/{$updatedOrder->order_code}");

                    Log::debug('LINE通知送信準備', [
                        'user_id' => $auth->id,
                        'line_user_id' => $auth->line_user_id,
                        'message' => $message
                    ]);

                    // LineMessagingServiceを使用してメッセージを送信
                    $result = $this->lineMessagingService->pushMessage($auth->line_user_id, $message);
                    
                    if ($result) {
                        Log::info('LINE通知送信成功', [
                            'user_id' => $auth->id,
                            'line_user_id' => $auth->line_user_id,
                            'order_code' => $updatedOrder->order_code
                        ]);
                    } else {
                        throw new \Exception('LINE通知の送信に失敗しました');
                    }

                } catch (\Exception $e) {
                    // LINE送信失敗のログを記録するが、注文処理は継続
                    Log::error('LINE通知の送信に失敗しました', [
                        'user_id' => $auth->id,
                        'line_user_id' => $auth->line_user_id,
                        'order_code' => $updatedOrder->order_code,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return $this->jsonResponse(
                self::ORDER_SUCCESS_MESSAGE, 
                ['order_code' => $updatedOrder->order_code]
            );

        } catch (\Exception $e) {
            Log::error('注文処理エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => ['user_id' => $auth->id]
            ]);
            return $this->jsonResponse(
                self::UNEXPECTED_ERROR_MESSAGE, 
                ['error' => $e->getMessage()], 
                500
            );
        }
    }
}
