<?php
/**
 * Webコントローラ
 * 顧客向けお気に入り商品管理機能
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\FavoriteItem\FavoriteItemService as FavoriteItemService;
use App\Services\ItemCategory\ItemCategoryService as ItemCategoryService;
use App\Services\Order\OrderService as OrderService;
use App\Services\OrderDetail\OrderDetailService as OrderDetailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteItemController extends Controller
{
    protected $favoriteItemService;
    protected $itemCategoryService;
    protected $orderService;
    protected $orderDetailService;

    public function __construct(
        FavoriteItemService $favoriteItemService,
        ItemCategoryService $itemCategoryService,
        OrderService $orderService,
        OrderDetailService $orderDetailService
    ) {
        $this->favoriteItemService = $favoriteItemService;
        $this->itemCategoryService = $itemCategoryService;
        $this->orderService = $orderService;
        $this->orderDetailService = $orderDetailService;
    }

    /**
     * index
     * お気に入り商品の一覧表示
     */
    public function index()
    {
        try {
            $auth = Auth::user();

            // カテゴリ一覧
            $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

            // お気に入り商品一覧
            $favoriteItems = $this->favoriteItemService->getUserFavorites($auth->id, $auth->site_id);

            // 未発注伝票に紐づく注文詳細一覧
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if ($unorderedOrder) {
                $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id)->toArray();
            } else {
                $unorderedItems = [];
            }

            $favoriteItems = $this->calculateScores($favoriteItems, $unorderedItems);
            Log::info('お気に入り商品一覧: ' . json_encode($favoriteItems));

            return view('customer.pages.favorite', compact('favoriteItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching favorite items: ' . $e->getMessage());
            return view('customer.pages.favorite', ['error' => __('お気に入り商品の取得に失敗しました。')]);
        }
    }

    private function calculateScores($favoriteItems, $unorderedItems)
    {
        $unorderedItemIds = array_column($unorderedItems, 'item_id');

        foreach ($favoriteItems as $key => $item) {
            $isUnordered = in_array($item['item_id'], $unorderedItemIds);
            $favoriteItems[$key]['score'] = $isUnordered ? 1 : 0;

            if ($isUnordered) {
                $index = array_search($item['item_id'], $unorderedItemIds);
                $favoriteItems[$key]['unorderedVolume'] = $unorderedItems[$index]['volume'];
            } else {
                $favoriteItems[$key]['unorderedVolume'] = 1;
            }
        }

        return $favoriteItems;
    }
}
