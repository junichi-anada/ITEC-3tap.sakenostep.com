<?php
/**
 * 固定ページ コントローラー
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    protected $itemCategoryReadService;

    public function __construct(ItemCategoryReadService $itemCategoryReadService)
    {
        $this->itemCategoryReadService = $itemCategoryReadService;
    }

    /**
     * 注文についてのページ
     *
     * @return \Illuminate\View\View
     */
    public function order()
    {
        return $this->renderPage('customer.about_order');
    }

    /**
     * 配送についてのページ
     *
     * @return \Illuminate\View\View
     */
    public function delivery()
    {
        return $this->renderPage('customer.about_delivery');
    }

    /**
     * ページをレンダリングする共通メソッド
     *
     * @param string $viewName
     * @return \Illuminate\View\View
     */
    private function renderPage($viewName)
    {
        try {
            $auth = Auth::user();
            $categories = $this->itemCategoryReadService->getListBySiteId($auth->site_id);

            return view($viewName, compact('categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return view($viewName, ['error' => __('ページの表示に失敗しました。')]);
        }
    }
}