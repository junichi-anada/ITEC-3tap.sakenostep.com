<?php
/**
 * 顧客向けおすすめ商品管理機能
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\Item\Customer\ReadService as ItemReadService;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use App\Services\FavoriteItem\Customer\ReadService as FavoriteItemReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;

class RecommendedItemController extends Controller
{
    protected $itemReadService;
    protected $favoriteItemReadService;
    protected $orderDetailReadService;
    protected $itemCategoryService;

    public function __construct(
        ItemReadService $itemReadService,
        FavoriteItemReadService $favoriteItemReadService,
        OrderDetailReadService $orderDetailReadService,
        ItemCategoryReadService $itemCategoryService
    ) {
        $this->itemReadService = $itemReadService;
        $this->favoriteItemReadService = $favoriteItemReadService;
        $this->orderDetailReadService = $orderDetailReadService;
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

            $categories = $this->getCategories($auth->site_id);
            $recommendedItems = $this->getRecommendedItems($auth->site_id);
            $favoriteItems = $this->getFavoriteItems($auth->id, $auth->site_id);
            $unorderedItems = $this->getUnorderedItems($auth->id, $auth->site_id);

            $recommendedItems = $this->calculateScores($recommendedItems, $favoriteItems, $unorderedItems);

            return view('customer.recommend', compact('recommendedItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching recommended items: ' . $e->getMessage());
            return view('customer.recommend', ['error' => __('おすすめ商品の取得に失敗しました。')]);
        }
    }

    private function getCategories($siteId)
    {
        return $this->itemCategoryService->getListBySiteId($siteId);
    }

    private function getRecommendedItems($siteId)
    {
        return $this->itemReadService->getRecommendedItems($siteId);
    }

    private function getFavoriteItems($userId, $siteId)
    {
        return $this->favoriteItemReadService->getItemIdListByUserAndSiteId($userId, $siteId);
    }

    private function getUnorderedItems($userId, $siteId)
    {
        $unorderedItems = $this->orderDetailReadService->getUnorderedListByUserIdAndSiteId($userId, $siteId);
        return $unorderedItems ? $unorderedItems->toArray() : [];
    }

    private function calculateScores($recommendedItems, $favoriteItems, $unorderedItems)
    {
        $unorderedItemIds = array_column($unorderedItems, 'item_id');

        foreach ($recommendedItems as $key => $item) {
            $recommendedItems[$key]['score1'] = in_array($item['id'], $unorderedItemIds) ? 1 : 0;
            $recommendedItems[$key]['score2'] = in_array($item['id'], $favoriteItems) ? 1 : 0;
            $recommendedItems[$key]['unorderedVolume'] = in_array($item['id'], $unorderedItems) ? $unorderedItems['volume'] : 1;
        }

        return $recommendedItems;
    }
}
