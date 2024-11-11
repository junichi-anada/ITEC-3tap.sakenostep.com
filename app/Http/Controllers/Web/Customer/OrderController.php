<?php
/**
 * Webコントローラ
 * 顧客向け注文管理機能
 */
namespace App\Http\Controllers\Web\Customer;

use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderDetailReadService;
    protected $itemCategoryReadService;

    public function __construct(
        OrderDetailReadService $orderDetailReadService,
        ItemCategoryReadService $itemCategoryReadService
    ) {
        $this->orderDetailReadService = $orderDetailReadService;
        $this->itemCategoryReadService = $itemCategoryReadService;
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

            $categories = $this->getCategories($auth->site_id);
            $orderItems = $this->getUnorderedItems($auth->id, $auth->site_id);

            return view('customer.order', compact('orderItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching unordered items: ' . $e->getMessage());
            return view('customer.order', ['error' => __('未発注の注文データの取得に失敗しました。')]);
        }
    }

    private function getCategories($siteId)
    {
        return $this->itemCategoryReadService->getListBySiteId($siteId);
    }

    private function getUnorderedItems($userId, $siteId)
    {
        return $this->orderDetailReadService->getUnorderedListWithItemsByUserIdAndSiteId($userId, $siteId);
    }
}
