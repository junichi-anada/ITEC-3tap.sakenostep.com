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
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;
use App\Services\Item\ItemService as ItemService;
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

    public function __construct(
        OrderDetailService $orderDetailService,
        OrderService $orderService,
        ItemService $itemService,
    ) {
        $this->orderDetailService = $orderDetailService;
        $this->orderService = $orderService;
        $this->itemService = $itemService;
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
            // 登録対象の商品情報を取得
            $item = $this->itemService->getByCodeOne($itemCode, $auth->site_id);

            // 未発注の伝票データを取得
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);

            // 未発注の伝票が存在しない場合は新規に作成
            if (is_null($order)) {
                $order = $this->orderService->createOrder($auth->site_id, $auth->id);
            }

            $data = [
                'detail_code' => Str::uuid(),
                'order_id' => $order->id,
                'item_id' => $item->id,
                'volume' => $volume,
                'unit_price' => $item->unit_price ?? 0,
                'unit_name' => $item->unit_name ?? "1",
                'price' => $item->price ?? 0,
                'tax' => $item->tax ?? 0,
            ];

            $orderDetail = $this->orderDetailService->createOrderDetail($data);

            return $this->jsonResponse(self::SUCCESS_MESSAGE, ['detail_code' => $orderDetail->detail_code], 201);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['error' => $e->getMessage()], 500);
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

        // 削除対象の商品情報を取得
        $item = $this->itemService->getByCodeOne($item_code, $auth->site_id);
        if (!$item) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }
        $itemId = $item->id;

        $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
        if (!$order) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }

        $process = $this->orderDetailService->deleteItemFromOrder($order->id, $itemId);
        if (!$process || is_null($process)) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }

        return $this->jsonResponse(self::DELETE_SUCCESS_MESSAGE);
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

        $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
        if (!$order) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }
        $orderId = $order->id;

        $process = $this->orderDetailService->deleteAllOrderDetails($orderId);
        if ($process) {
            return $this->jsonResponse(self::DELETE_ALL_SUCCESS_MESSAGE);
        }

        return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
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

        $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
        if (!$order) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }
        $orderId = $order->id;

        $order = $this->orderService->updateOrderDate($orderId);
        if (!$order) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }

        return $this->jsonResponse(self::ORDER_SUCCESS_MESSAGE, ['order_code' => $order->order_code]);
    }

}
