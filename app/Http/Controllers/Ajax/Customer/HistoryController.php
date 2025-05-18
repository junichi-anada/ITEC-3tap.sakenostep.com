<?php

namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use App\Services\OrderDetail\OrderDetailService;
use App\Services\OrderDetail\DTOs\OrderDetailData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Item;

class HistoryController extends Controller
{
    protected $orderService;
    protected $orderDetailService;

    public function __construct(
        OrderService $orderService,
        OrderDetailService $orderDetailService
    ) {
        $this->orderService = $orderService;
        $this->orderDetailService = $orderDetailService;
    }

    private function jsonResponse($message, $data = [], $status = 200)
    {
        return response()->json(array_merge(['message' => $message], $data), $status);
    }

    /**
     * 注文履歴のアイテムを未注文リストに追加
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAll(Request $request)
    {
        $auth = Auth::user();
        $orderCode = $request->input('order_code');

        try {
            // order_codeを使用して注文を取得
            $order = $this->orderService->getByOrderCode($orderCode);
            Log::info('order: ' . json_encode($order));

            // orderDetailのReadServiceを使用して注文詳細を取得
            $orderDetails = $this->orderDetailService->getOrderDetailsByOrderId($order->id);
            Log::info('orderDetails: ' . json_encode($orderDetails));

            if (!$order || $order->user_id !== $auth->entity_id) {
                return $this->jsonResponse('注文が見つかりません。', [], 404);
            }

            // 未発注の注文基本データを取得
            $currentOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->entity_id, $auth->site_id);

            // 未発注の注文基本データがない場合、新しい注文基本データを作成
            if (!$currentOrder) {
                // OrderData::fromRequest() を使用して配列を OrderData オブジェクトに変換
                $orderData = \App\Services\Order\DTOs\OrderData::fromRequest([
                    'site_id' => $auth->site_id,
                    'user_id' => $auth->entity_id
                ]);
                $currentOrder = $this->orderService->create($orderData);
            }

            // 注文履歴のアイテムを未注文の注文に追加
            foreach ($orderDetails as $orderDetail) {
                // orderDetailがオブジェクトであることを確認
                if (is_object($orderDetail)) {
                    $orderDetailData = new OrderDetailData(
                        userId: $auth->entity_id,
                        siteId: $auth->site_id,
                        orderId: $currentOrder->id,
                        itemId: $orderDetail->item_id,
                        volume: $orderDetail->volume
                    );

                    $this->orderDetailService->addOrderDetail($orderDetailData);
                } else {
                    Log::warning('Order detail is not an object: ' . json_encode($orderDetail));
                }
            }

            return $this->jsonResponse('注文履歴のアイテムが未注文リストに追加されました。', ['redirect' => route('user.order.item.list')], 200);
        } catch (\Exception $e) {
            Log::error('Error adding all items to order history: ' . $e->getMessage());
            return $this->jsonResponse('すべてのアイテムを注文履歴に追加することに失敗しました。', ['error' => $e->getMessage()], 500);
        }
    }
}
