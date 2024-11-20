<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\Item\ItemService as ItemService;
use App\Services\FavoriteItem\FavoriteItemService as FavoriteItemService;
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;
use App\Services\ItemCategory\ItemCategoryService as ItemCategoryService;

class SearchController extends Controller
{
    protected $itemService;
    protected $favoriteItemService;
    protected $orderService;
    protected $orderDetailService;
    protected $itemCategoryService;

    public function __construct(
        ItemService $itemService,
        FavoriteItemService $favoriteItemService,
        OrderService $orderService,
        OrderDetailService $orderDetailService,
        ItemCategoryService $itemCategoryService
    ) {
        $this->itemService = $itemService;
        $this->favoriteItemService = $favoriteItemService;
        $this->orderService = $orderService;
        $this->orderDetailService = $orderDetailService;
        $this->itemCategoryService = $itemCategoryService;
    }

    /**
     * 検索結果の一覧表示
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $keyword = $this->validateKeyword($request);
            if (empty($keyword)) {
                $keyword ="";
            }
            $auth = Auth::user();

            // カテゴリ一覧
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);
           // 商品一覧
            $items = $this->itemService->searchByKeyword($keyword, $auth->site_id);
            if ($items) {
                $items = $items->toArray();
            } else {
                $items = [];
            }
            // お気に入り商品一覧
            $favoriteItems = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);
            if ($favoriteItems) {
                $favoriteItems = $favoriteItems->toArray();
            } else {
                $favoriteItems = [];
            }
            // 未発注伝票に紐づく注文詳細一覧
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
            if ($unorderedItems) {
                $unorderedItems = $unorderedItems->toArray();
            } else {
                $unorderedItems = [];
            }

            $items = $this->calculateScores($items, $favoriteItems, $unorderedItems);

            return view('customer.search', compact('items', 'keyword', 'favoriteItems', 'unorderedItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching search results: ' . $e->getMessage());
            return view('customer.search', ['error' => __('検索結果の取得に失敗しました。')]);
        }
    }

    private function validateKeyword(Request $request)
    {
        return $request->validate([
            'keyword' => 'nullable|string|max:255',
        ])['keyword'];
    }

    /**
     * 商品のスコアを計算
     *
     * @param array $items 商品一覧
     * @param array $favoriteItems お気に入り商品ID一覧
     * @param array $unorderedItems 未注文商品一覧
     * @return array スコア計算済み商品一覧
     */
    private function calculateItemScores(array $items, array $favoriteItems, array $unorderedItems): array
    {
        foreach ($items as $key => $item) {
            $items[$key]['score1'] = in_array($item['id'], array_column($unorderedItems, 'item_id')) ? 1 : 0;
            $items[$key]['score2'] = in_array($item['id'], array_column($favoriteItems, 'item_id')) ? 1 : 0;
            $items[$key]['unorderedVolume'] = 1;
        }
        return $items;
    }
}
