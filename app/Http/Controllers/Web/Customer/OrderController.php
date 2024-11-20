<?php
/**
 * Webコントローラ
 * 顧客向け注文管理機能
 */
namespace App\Http\Controllers\Web\Customer;

use App\Services\ItemCategory\ItemCategoryService as ItemCategoryService;
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderDetailService;
    protected $itemCategoryService;
    protected $orderService;

    public function __construct(
        OrderDetailService $orderDetailService,
        ItemCategoryService $itemCategoryService,
        OrderService $orderService
    ) {
        $this->orderDetailService = $orderDetailService;
        $this->itemCategoryService = $itemCategoryService;
        $this->orderService = $orderService;
    }

    /**
     * 未注文商品リストの一覧表示
     * 未発注の注文に登録されている商品一覧を表示する。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $auth = Auth::user();

            // カテゴリ一覧の取得
            $categories = $this->itemCategoryService->getBySiteId($auth->site_id);

            // 最新の未発注伝票を確認
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);

            if ($order) {
                $orderItems = $this->orderDetailService->getOrderDetailsByOrderId($order->id);
            } else {
                $orderItems = [];
            }

            return view('customer.order', compact('orderItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching unordered items: ' . $e->getMessage());
            return view('customer.order', ['error' => __('未発注の注文データの取得に失敗しました。')]);
        }
    }
}
