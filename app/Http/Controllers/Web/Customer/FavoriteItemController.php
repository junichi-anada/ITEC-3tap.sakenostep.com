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
use App\Services\FavoriteItem\Customer\ReadService as FavoriteItemReadService;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteItemController extends Controller
{
    protected $favoriteItemReadService;
    protected $itemCategoryReadService;
    protected $orderDetailReadService;

    public function __construct(
        FavoriteItemReadService $favoriteItemReadService,
        ItemCategoryReadService $itemCategoryReadService,
        OrderDetailReadService $orderDetailReadService
    ) {
        $this->favoriteItemReadService = $favoriteItemReadService;
        $this->itemCategoryReadService = $itemCategoryReadService;
        $this->orderDetailReadService = $orderDetailReadService;
    }

    /**
     * index
     * お気に入り商品の一覧表示
     */
    public function index()
    {
        try {
            $auth = Auth::user();

            $categories = $this->getCategories($auth->site_id);
            $favoriteItems = $this->getFavoriteItems($auth->id, $auth->site_id);
            $unorderedItems = $this->getUnorderedItems($auth->id, $auth->site_id);

            $favoriteItems = $this->calculateScores($favoriteItems, $unorderedItems);

            return view('customer.favorite', compact('favoriteItems', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching favorite items: ' . $e->getMessage());
            return view('customer.favorite', ['error' => __('お気に入り商品の取得に失敗しました。')]);
        }
    }

    private function getCategories($siteId)
    {
        return $this->itemCategoryReadService->getListBySiteId($siteId);
    }

    private function getFavoriteItems($userId, $siteId)
    {
        return $this->favoriteItemReadService->getListWithItemDetailsByUserAndSiteId($userId, $siteId);
    }

    private function getUnorderedItems($userId, $siteId)
    {
        return $this->orderDetailReadService->getUnorderedListByUserIdAndSiteId($userId, $siteId)->toArray();
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
