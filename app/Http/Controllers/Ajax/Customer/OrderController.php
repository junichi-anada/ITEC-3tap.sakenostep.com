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

            // LINE通知の送信処理 (フィーチャーフラグにより制御)
            // 注文ボタン（カート追加時）のLINE通知は config('features.enable_line_notification', false) で制御します。
            // 詳細はプロジェクトの設定ドキュメント (README.md および config/features.php) を参照してください。
            $isLineNotificationEnabled = config('features.enable_line_notification', false);
            if ($isLineNotificationEnabled && $auth->line_user_id) {
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
                        Log::info('LINE通知送信成功 (カート追加時)', [
                            'user_id' => $auth->id,
                            'line_user_id' => $auth->line_user_id,
                            'order_code' => $orderDetail->order_code
                        ]);
                    } else {
                        throw new \Exception('LINE通知の送信に失敗しました (カート追加時)');
                    }

                } catch (\Exception $e) {
                    // LINE送信失敗のログを記録するが、注文処理は継続
                    Log::error('LINE通知の送信に失敗しました: ' . $e->getMessage(), [
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
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->entity_id, $auth->site_id);
            if (!$order) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $orderDetailData = new OrderDetailData(
                userId: $auth->entity_id,
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
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->entity_id, $auth->site_id);
            if (!$order) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $orderDetailData = new OrderDetailData(
                userId: $auth->entity_id,
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
    public function order(Request $request) // Request $request を引数に追加
    {
        $auth = $this->getAuthenticatedUser();

        // リクエストデータのバリデーション
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_code' => 'required|string|exists:items,item_code',
            'items.*.volume' => 'required|integer|min:1',
        ]);

        Log::debug('[OrderController@order] User authentication info', [
            'user_id' => $auth->entity_id,
            'line_user_id' => $auth->line_user_id ?? null,
            'has_line' => !empty($auth->line_user_id),
            'request_items' => $validated['items']
        ]);

        DB::beginTransaction();
        try {
            // 未発注の注文を取得する際に、Authenticate ID ($auth->id) ではなく User ID ($auth->entity_id) を使用
            $currentOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->entity_id, $auth->site_id);
            if (!$currentOrder) {
                // ログ出力時も正しいユーザーIDを使用
                Log::error('[OrderController@order] No unordered order found for user.', ['user_id' => $auth->entity_id, 'site_id' => $auth->site_id]);
                DB::rollBack();
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, ['message' => '注文対象のカートが見つかりません。'], 404);
            }

            // 各注文詳細の数量を更新
            foreach ($validated['items'] as $itemData) {
                $item = $this->itemService->getByCode($itemData['item_code'], $auth->site_id);
                if (!$item) {
                    Log::error('[OrderController@order] Item not found or does not belong to site.', ['item_code' => $itemData['item_code'], 'site_id' => $auth->site_id]);
                    DB::rollBack();
                    return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['message' => "商品コード {$itemData['item_code']} が見つかりません。"], 400);
                }

                // OrderDetailServiceに数量更新メソッドを呼び出す (後でOrderDetailServiceに実装)
                // 既存のOrderDetailを特定し、数量と価格、税を更新する想定
                $success = $this->orderDetailService->updateOrAddOrderDetailByItem(
                    $currentOrder->id,
                    $item->id, // item_id を渡す
                    $itemData['volume'],
                    $auth->id,
                    $auth->site_id,
                    $item // Itemモデルも渡して価格計算等に利用
                );

                if (!$success) {
                    Log::error('[OrderController@order] Failed to update order detail.', ['order_id' => $currentOrder->id, 'item_id' => $item->id, 'volume' => $itemData['volume']]);
                    DB::rollBack();
                    return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['message' => "商品 {$item->name} の数量更新に失敗しました。"], 500);
                }
            }

            // 注文日を更新して注文を確定
            $updatedOrder = $this->orderService->updateOrderDate($currentOrder->id);
            if (!$updatedOrder) {
                Log::error('[OrderController@order] Failed to update order date (finalize order).', ['order_id' => $currentOrder->id]);
                DB::rollBack();
                return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['message' => '注文の確定処理に失敗しました。'], 500);
            }

            DB::commit();

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
