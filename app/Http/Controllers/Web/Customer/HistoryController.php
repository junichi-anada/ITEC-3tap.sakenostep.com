<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\Order\Customer\ReadService as OrderReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    protected $orderReadService;
    protected $orderDetailReadService;
    protected $itemCategoryReadService;

    public function __construct(
        OrderReadService $orderReadService,
        OrderDetailReadService $orderDetailReadService,
        ItemCategoryReadService $itemCategoryReadService
    ) {
        $this->orderReadService = $orderReadService;
        $this->orderDetailReadService = $orderDetailReadService;
        $this->itemCategoryReadService = $itemCategoryReadService;
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
            $orders = $this->orderReadService->getOrdersByUserId($auth->id);
            $categories = $this->itemCategoryReadService->getListBySiteId($auth->site_id);

            return view('customer.history', compact('orders', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching order history: ' . $e->getMessage());
            return view('customer.history', ['error' => __('注文履歴の取得に失敗しました。')]);
        }
    }

    /**
     * 注文履歴の詳細表示
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function show($orderId)
    {
        try {
            $auth = Auth::user();
            $order = $this->orderReadService->getOrderByIdAndUserId($orderId, $auth->id);
            $orderDetails = $this->orderDetailReadService->getDetailsByOrderId($orderId);
            $categories = $this->itemCategoryReadService->getListBySiteId($auth->site_id);

            return view('customer.history_detail', compact('order', 'orderDetails', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return view('customer.history_detail', ['error' => __('注文詳細の取得に失敗しました。')]);
        }
    }
}
