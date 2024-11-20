<?php
/**
 * 顧客向けおすすめ商品管理機能
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\Item\ItemService as ItemService;
use App\Services\ItemCategory\ItemCategoryService as ItemCategoryService;
use App\Services\FavoriteItem\FavoriteItemService as FavoriteItemService;
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;

class RecommendedItemController extends Controller
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
     * おすすめ商品の一覧表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $auth = Auth::user();

            // カテゴリ一覧
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);
            // おすすめ商品一覧
            $recommendedItems = $this->itemService->getRecommendedItems($auth->site_id);
            if ($recommendedItems) {
                $recommendedItems = $recommendedItems->toArray();
            } else {
                $recommendedItems = [];
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

            $recommendedItems = $this->calculateItemScores($recommendedItems, $favoriteItems, $unorderedItems);

            return view('customer.recommend', compact('recommendedItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching recommended items: ' . $e->getMessage());
            return view('customer.recommend', ['error' => __('おすすめ商品の取得に失敗しました。')]);
        }
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
