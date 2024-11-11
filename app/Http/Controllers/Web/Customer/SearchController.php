<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\Item\Customer\ReadService as ItemReadService;
use App\Services\FavoriteItem\Customer\ReadService as FavoriteItemReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;

class SearchController extends Controller
{
    protected $itemReadService;
    protected $favoriteItemReadService;
    protected $orderDetailReadService;
    protected $itemCategoryReadService;

    public function __construct(
        ItemReadService $itemReadService,
        FavoriteItemReadService $favoriteItemReadService,
        OrderDetailReadService $orderDetailReadService,
        ItemCategoryReadService $itemCategoryReadService
    ) {
        $this->itemReadService = $itemReadService;
        $this->favoriteItemReadService = $favoriteItemReadService;
        $this->orderDetailReadService = $orderDetailReadService;
        $this->itemCategoryReadService = $itemCategoryReadService;
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
            $auth = Auth::user();

            $categories = $this->getCategories($auth->site_id);
            $items = $this->getItems($auth->site_id, $keyword);
            $favoriteItems = $this->getFavoriteItems($auth->id, $auth->site_id);
            $unorderedItems = $this->getUnorderedItems($auth->id, $auth->site_id);

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

    private function getCategories($siteId)
    {
        return $this->itemCategoryReadService->getListBySiteId($siteId);
    }

    private function getItems($siteId, $keyword)
    {
        if (!empty($keyword)) {
            return $this->itemReadService->searchByKeyword($siteId, $keyword);
        }
        return $this->itemReadService->getListBySiteId($siteId);
    }

    private function getFavoriteItems($userId, $siteId)
    {
        return $this->favoriteItemReadService->getItemIdListByUserAndSiteId($userId, $siteId);
    }

    private function getUnorderedItems($userId, $siteId)
    {
        return $this->orderDetailReadService->getUnorderedListByUserIdAndSiteId($userId, $siteId)->toArray();
    }

    private function calculateScores($items, $favoriteItems, $unorderedItems)
    {
        $unorderedItemIds = array_column($unorderedItems, 'item_id');

        foreach ($items as $key => $item) {
            $items[$key]['score1'] = in_array($item['id'], $unorderedItemIds) ? 1 : 0;
            $items[$key]['score2'] = in_array($item['id'], $favoriteItems) ? 1 : 0;
            $items[$key]['unorderedVolume'] = in_array($item['id'], $unorderedItems) ? $unorderedItems['volume'] : 1;
        }

        return $items;
    }
}
