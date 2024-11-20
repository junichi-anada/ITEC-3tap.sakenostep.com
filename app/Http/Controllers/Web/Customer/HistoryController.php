<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;
use App\Services\ItemCategory\ItemCategoryService as ItemCategoryService;
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

            // カテゴリ取得
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

            // 注文履歴取得
            $orders = $this->orderService->getOrderedOrdersByUserAndSite($auth->id, $auth->site_id);
            if (!$orders) {
                return view('customer.history', ['error' => __('注文履歴が見つかりません。')]);
            }

            return view('customer.history', compact('orders', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching order history: ' . $e->getMessage());
            return view('customer.history', ['error' => __('注文履歴の取得に失敗しました。')]);
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
            // order_codeを使用して注文を取得
            $order = $this->orderService->getByOrderCode($orderCode);

            if (!$order || $order->user_id !== $auth->id) {
                return view('customer.history_detail', ['error' => __('注文が見つかりません。')]);
            }

            $orderDetails = $this->orderDetailService->getDetailsByOrderId($order->id);
            $categories = $this->itemCategoryService->getListBySiteId($auth->site_id);

            return view('customer.history_detail', compact('order', 'orderDetails', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return view('customer.history_detail', ['error' => __('注文詳細の取得に失敗しました。')]);
        }
    }
}
