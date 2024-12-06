<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\Item\ItemService;
use App\Services\ItemCategory\ItemCategoryService;
use App\Services\Order\OrderService;
use App\Services\OrderDetail\OrderDetailService;
use App\Services\FavoriteItem\FavoriteItemService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct(
        private ItemService $itemService,
        private ItemCategoryService $itemCategoryService,
        private OrderService $orderService,
        private OrderDetailService $orderDetailService,
        private FavoriteItemService $favoriteItemService
    ) {}

    public function index()
    {
        try {
            $auth = Auth::user();

            // カテゴリ一覧の取得
            $categories = $this->itemCategoryService->getAllCategories($auth->site_id);

            // お気に入り商品の取得
            $favoriteItems = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);
            $favoriteItemIds = $favoriteItems ? $favoriteItems->pluck('item_id')->toArray() : [];

            // 未発注の注文を取得
            $unorderedItems = collect([]);
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if ($unorderedOrder) {
                $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
            }

            return view('customer.pages.category', compact(
                'categories',
                'favoriteItemIds',
                'unorderedItems'
            ));
        } catch (\Exception $e) {
            Log::error('Error in category index: ' . $e->getMessage());
            return redirect()->route('user.search.item.list')
                ->with('error', 'カテゴリ一覧の取得に失敗しました。');
        }
    }

    public function show($code)
    {
        try {
            $auth = Auth::user();
            Log::info('Attempting to find category by code', ['code' => $code, 'site_id' => $auth->site_id]);

            // カテゴリの取得
            $category = $this->itemCategoryService->getByCategoryCode($auth->site_id, $code);

            if (!$category) {
                Log::warning('Category not found', ['code' => $code, 'site_id' => $auth->site_id]);
                return redirect()->route('user.search.item.list')
                    ->with('error', 'カテゴリが見つかりません。');
            }

            Log::info('Category found', ['category_id' => $category->id, 'category_name' => $category->name]);

            // カテゴリ一覧の取得
            $categories = $this->itemCategoryService->getAllCategories($auth->site_id);

            // 商品一覧の取得
            Log::info('Fetching items for category', ['category_id' => $category->id]);
            $items = $this->itemService->getListBySiteIdAndCategoryId($auth->site_id, $category->id);
            Log::info('Items fetched', ['items_count' => $items->count()]);

            // お気に入り商品の取得
            $favoriteItems = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);
            $favoriteItemIds = $favoriteItems ? $favoriteItems->pluck('item_id')->toArray() : [];

            // 未発注の注文を取得
            $unorderedItems = collect([]);
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if ($unorderedOrder) {
                $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
            }

            // 商品データの整形
            $items = $items->map(function ($item) use ($favoriteItemIds, $unorderedItems) {
                $unorderedDetail = $unorderedItems->firstWhere('item_id', $item->id);
                return [
                    'id' => $item->id,
                    'item_code' => $item->item_code,
                    'name' => $item->name,
                    'maker_name' => $item->maker_name,
                    'score1' => $unorderedDetail ? 1 : 0, // 注文リストに存在するかどうか
                    'score2' => in_array($item->id, $favoriteItemIds) ? 1 : 0, // お気に入りに存在するかどうか
                    'unorderedVolume' => $unorderedDetail ? $unorderedDetail->volume : 0 // 未発注の注文数量
                ];
            });

            return view('customer.pages.category_item', compact(
                'category',
                'categories',
                'items',
                'favoriteItemIds',
                'unorderedItems'
            ));
        } catch (\Exception $e) {
            Log::error('Error in category show: ' . $e->getMessage(), [
                'code' => $code,
                'exception' => $e
            ]);
            return redirect()->route('user.search.item.list')
                ->with('error', 'カテゴリ情報の取得に失敗しました。');
        }
    }
}
