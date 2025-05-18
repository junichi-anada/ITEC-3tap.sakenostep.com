<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;
use App\Services\ItemCategory\ItemCategoryService as ItemCategoryService;
use App\Services\Order\DTOs\OrderSearchCriteria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    protected $orderService;
    protected $orderDetailService;
    protected $itemCategoryService;

    public function __construct(
        OrderService $orderService,
        OrderDetailService $orderDetailService,
        ItemCategoryService $itemCategoryService
    ) {
        $this->orderService = $orderService;
        $this->orderDetailService = $orderDetailService;
        $this->itemCategoryService = $itemCategoryService;
    }

    /**
     * 注文履歴の一覧表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $auth = Auth::user();
            $message = null;

            // カテゴリ取得
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

            // 注文履歴取得
            $criteria = new OrderSearchCriteria(
                userId: $auth->entity_id,
                siteId: $auth->site_id,
                isOrdered: true,
                orderBy: ['ordered_at' => 'desc']
            );

            $orders = $this->orderService->search($criteria);
            if ($orders->isEmpty()) {
                $message = __('注文履歴が見つかりません。');
                return view('customer.pages.history', compact('message', 'categories', 'orders'));
            }

            return view('customer.pages.history', compact('orders', 'categories', 'message'));
        } catch (\Exception $e) {
            Log::error('Error fetching order history: ' . $e->getMessage());
            $message = __('注文履歴の取得に失敗しました。');
            $categories = collect([]);
            $orders = collect([]);
            return view('customer.pages.history', compact('message', 'categories', 'orders'));
        }
    }

    /**
     * 注文履歴の詳細表示
     *
     * @param string $orderCode
     * @return \Illuminate\View\View
     */
    public function show($orderCode)
    {
        try {
            $auth = Auth::user();
            $message = null;

            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

            // order_codeを使用して注文を取得
            $order = $this->orderService->getByOrderCode($orderCode);

            if (!$order || $order->user_id !== $auth->id) {
                $message = __('注文が見つかりません。');
                $orderDetails = collect([]);
                return view('customer.pages.history_detail', compact('message', 'categories', 'order', 'orderDetails'));
            }

            $orderDetails = $this->orderDetailService->getOrderDetailsByOrderId($order->id);

            // --- ここから追加 ---
            // 未発注の注文基本データを取得
            $currentOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);

            // 未注文リストのアイテムIDのコレクションを作成
            $unorderedItemIds = collect();
            if ($currentOrder) {
                 $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($currentOrder->id);
                 $unorderedItemIds = $unorderedItems->pluck('item_id');
            }

            // 各注文詳細アイテムが未注文リストに含まれているかフラグを設定
            $orderDetails->each(function ($orderDetail) use ($unorderedItemIds) {
                $orderDetail->isInOrderList = $unorderedItemIds->contains($orderDetail->item_id);
            });
            // --- ここまで追加 ---

            return view('customer.pages.history_detail', compact('order', 'orderDetails', 'categories', 'message'));
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            $message = __('注文詳細の取得に失敗しました。');
            $categories = collect([]);
            $order = null;
            $orderDetails = collect([]);
            return view('customer.pages.history_detail', compact('message', 'categories', 'order', 'orderDetails'));
        }
    }
}
