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

            $recommendedItems = "";
            $favoriteItems = "";
            $unorderedItems = "";

            // カテゴリ一覧
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

            // おすすめ商品一覧
            $recommendedItems = $this->itemService->getRecommendedItems($auth->site_id);
            if (!is_null($recommendedItems)) {
                $recommendedItems = $recommendedItems->toArray();
            }

            // お気に入り商品一覧
            $favoriteItems = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);
            if (!is_null($favoriteItems)) {
                $favoriteItems = $favoriteItems->toArray();
            }

            // 未発注伝票に紐づく注文詳細一覧
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if (!is_null($unorderedOrder)) {
                $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
                if (!is_null($unorderedItems)) {
                    $unorderedItems = $unorderedItems->toArray();
                }
            }

            if (!is_array($recommendedItems)) {
                $recommendedItems = [];
            }

            if (!is_array($favoriteItems)) {
                $favoriteItems = [];
            }

            if (!is_array($unorderedItems)) {
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
        // お気に入り商品が空の場合は、スコア2を0にする
        if (empty($favoriteItems)) {
            foreach ($items as $key => $item) {
                $items[$key]['score2'] = 0;
            }
        }else {
            foreach ($items as $key => $item) {
                $items[$key]['score2'] = in_array($item['id'], array_column($favoriteItems, 'item_id')) ? 1 : 0;
                Log::info('key: ' . json_encode($key));
                Log::info('item_id: ' . json_encode($item['id']));
                Log::info('お気に入り商品があるの場合のスコア2: ' . json_encode($items[$key]['score2']));
                Log::info('お気に入り商品一覧: ' . json_encode($favoriteItems));
            }
        }

        // 未注文商品が空の場合は、スコア1を0で返却
        if (empty($unorderedItems)) {
            foreach ($items as $key => $item) {
                $items[$key]['score1'] = 0;
                $items[$key]['unorderedVolume'] = 1;
            }
            return $items;
        } else {
            foreach ($items as $key => $item) {
                $items[$key]['score1'] = in_array($item['id'], array_column($unorderedItems, 'item_id')) ? 1 : 0;
                $items[$key]['unorderedVolume'] = 1;
            }
        }

        // foreach ($items as $key => $item) {
        //     $items[$key]['score1'] = in_array($item['id'], array_column($unorderedItems, 'item_id')) ? 1 : 0;
        //     $items[$key]['score2'] = in_array($item['id'], array_column($favoriteItems, 'item_id')) ? 1 : 0;
        //     $items[$key]['unorderedVolume'] = 1;
        // }

        return $items;
    }
}
