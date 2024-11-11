<?php

namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Controller;
use App\Services\Order\Customer\ReadService as OrderReadService;
use App\Services\Order\Customer\CreateService as OrderCreateService;
use App\Services\OrderDetail\Customer\CreateService as OrderDetailCreateService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Item;

class HistoryController extends Controller
{
    protected $orderReadService;
    protected $orderCreateService;
    protected $orderDetailCreateService;
    protected $orderDetailReadService;

    public function __construct(
        OrderReadService $orderReadService,
        OrderCreateService $orderCreateService,
        OrderDetailCreateService $orderDetailCreateService,
        OrderDetailReadService $orderDetailReadService
    ) {
        $this->orderReadService = $orderReadService;
        $this->orderCreateService = $orderCreateService;
        $this->orderDetailCreateService = $orderDetailCreateService;
        $this->orderDetailReadService = $orderDetailReadService;
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
            $order = $this->orderReadService->getByOrderCode($orderCode);
            Log::info('order: ' . json_encode($order));

            // orderDetailのReadServiceを使用して注文詳細を取得
            $orderDetails = $this->orderDetailReadService->getDetailsByOrderId($order->id);
            Log::info('orderDetails: ' . json_encode($orderDetails));

            if (!$order || $order->user_id !== $auth->id) {
                return $this->jsonResponse('注文が見つかりません。', [], 404);
            }

            // 未発注の注文基本データを取得
            $currentOrder = $this->orderReadService->getUnorderedByUserIdAndSiteId($auth->id, $auth->site_id);

            // 未発注の注文基本データがない場合、新しい注文基本データを作成
            if (!$currentOrder) {
                $currentOrder = $this->orderCreateService->createBaseOrder($auth->site_id, $auth->id);
            }

            // 注文履歴のアイテムを未注文の注文に追加
            foreach ($orderDetails as $orderDetail) {
                // orderDetailがオブジェクトであることを確認
                if (is_object($orderDetail)) {
                    $this->orderDetailCreateService->createFromOrderDetail($currentOrder->id, $orderDetail);
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
