<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\Item\ItemService;
use App\Services\ItemCategory\ItemCategoryService;
use App\Services\Order\OrderService;
use App\Services\OrderDetail\OrderDetailService;
use App\Services\FavoriteItem\FavoriteItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function __construct(
        private ItemService $itemService,
        private ItemCategoryService $itemCategoryService,
        private OrderService $orderService,
        private OrderDetailService $orderDetailService,
        private FavoriteItemService $favoriteItemService
    ) {}

    public function index(Request $request)
    {
        try {
            $auth = Auth::user();
            $keyword = $request->input('keyword');
            
            // カテゴリ一覧の取得
            $categories = $this->itemCategoryService->getAllCategories($auth->site_id);

            // 商品一覧の取得
            $items = collect([]);
            if ($keyword) {
                $items = $this->itemService->searchByKeyword($keyword, $auth->site_id);
            }

            // お気に入り商品の取得
            $favoriteItems = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);
            $favoriteItemIds = $favoriteItems ? $favoriteItems->pluck('item_id')->toArray() : [];

            // 未発注の注文を取得
            $unorderedItems = collect([]);
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if ($unorderedOrder) {
                $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
            }

            return view('customer.pages.search', compact(
                'keyword',
                'categories',
                'items',
                'favoriteItemIds',
                'unorderedItems'
            ));
        } catch (\Exception $e) {
            Log::error('Error in search index: ' . $e->getMessage());
            return back()->with('error', '商品検索に失敗しました。');
        }
    }
}
