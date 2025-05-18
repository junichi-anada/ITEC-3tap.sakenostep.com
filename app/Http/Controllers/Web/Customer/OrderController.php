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
            $message = null;
            $orderItems = [];

            // カテゴリ一覧の取得
            $categories = $this->itemCategoryService->getAllCategories($auth->site_id);

            // 最新の未発注伝票を確認
            $order = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->entity_id, $auth->site_id);

            // 未発注伝票が存在しない場合
            if (!$order) {
                // 最新の発注済み伝票を取得
                $latestOrderedOrder = $this->orderService->getLatestOrderedOrderByUserAndSite($auth->entity_id, $auth->site_id);
                
                if ($latestOrderedOrder) {
                    // 最新の発注済み伝票から新規の未発注伝票を作成
                    $order = $this->orderService->createUnorderedOrderFromLatestOrdered($auth->entity_id, $auth->site_id);
                    
                    if ($order && $latestOrderedOrder->ordered_at) {
                        $orderDate = $latestOrderedOrder->ordered_at instanceof \DateTime 
                            ? $latestOrderedOrder->ordered_at->format('Y年m月d日')
                            : date('Y年m月d日', strtotime($latestOrderedOrder->ordered_at));
                        $message = "{$orderDate}の注文情報です。";
                    }
                } else {
                    // 発注済み伝票も存在しない場合
                    $message = "注文リストがありません。";
                    return view('customer.pages.order', compact('categories', 'orderItems', 'message'));
                }
            }

            if ($order) {
                $orderItems = $this->orderDetailService->getOrderDetailsByOrderId($order->id);
            }

            return view('customer.pages.order', compact('orderItems', 'categories', 'message'));
        } catch (\Exception $e) {
            Log::error('Error fetching unordered items: ' . $e->getMessage());
            $message = __('未発注の注文データの取得に失敗しました。');
            $categories = collect([]);
            $orderItems = [];
            return view('customer.pages.order', compact('message', 'categories', 'orderItems'));
        }
    }
}
