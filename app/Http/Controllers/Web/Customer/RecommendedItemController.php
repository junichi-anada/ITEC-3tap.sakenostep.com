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
            $message = null;

            // 初期値を配列で設定
            $recommendedItems = [];
            $favoriteItems = [];
            $unorderedItems = [];

            // カテゴリ一覧
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

            // おすすめ商品一覧
            $recommendedResult = $this->itemService->getRecommendedItems($auth->site_id);
            if ($recommendedResult) {
                $recommendedItems = $recommendedResult->toArray();
            }

            // お気に入り商品一覧
            $favoriteResult = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);
            if ($favoriteResult) {
                $favoriteItems = $favoriteResult->toArray();
            }

            // 未発注伝票に紐づく注文詳細一覧
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if ($unorderedOrder) {
                $unorderedResult = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
                if ($unorderedResult) {
                    $unorderedItems = $unorderedResult->toArray();
                }
            }

            $recommendedItems = $this->calculateItemScores($recommendedItems, $favoriteItems, $unorderedItems);

            return view('customer.pages.recommend', compact('recommendedItems', 'categories', 'message'));
        } catch (\Exception $e) {
            Log::error('Error fetching recommended items: ' . $e->getMessage());
            $message = __('おすすめ商品の取得に失敗しました。');
            $recommendedItems = [];
            $categories = collect([]);
            return view('customer.pages.recommend', compact('message', 'recommendedItems', 'categories'));
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
        if (empty($items)) {
            return [];
        }

        foreach ($items as $key => $item) {
            // デフォルト値を設定
            $items[$key]['score1'] = 0;
            $items[$key]['score2'] = 0;
            $items[$key]['unorderedVolume'] = 1;

            // お気に入り商品のスコアを計算
            if (!empty($favoriteItems)) {
                $items[$key]['score2'] = in_array($item['id'], array_column($favoriteItems, 'item_id')) ? 1 : 0;
            }

            // 未注文商品のスコアを計算
            if (!empty($unorderedItems)) {
                $items[$key]['score1'] = in_array($item['id'], array_column($unorderedItems, 'item_id')) ? 1 : 0;
            }
        }

        return $items;
    }
}
