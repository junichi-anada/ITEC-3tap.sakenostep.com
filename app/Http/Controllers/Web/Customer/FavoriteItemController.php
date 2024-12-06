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

class FavoriteItemController extends Controller
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

            // お気に入り商品の取得（itemリレーションをロード）
            $favoriteItems = $this->favoriteItemService->getUserFavorites(
                $auth->id,
                $auth->site_id,
                ['created_at' => 'desc'],
                ['item']  // itemリレーションをロード
            );

            // 未発注の注文を取得
            $unorderedItems = collect([]);
            $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
            if ($unorderedOrder) {
                $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId($unorderedOrder->id);
            }

            // お気に入り商品に対してスコアを計算
            foreach ($favoriteItems as $favoriteItem) {
                // 未注文リストに存在するかどうかのスコアを設定
                $favoriteItem->score = $unorderedItems->contains('item_id', $favoriteItem->item->id) ? 1 : 0;

                // 未注文リストに存在する場合、その数量を設定
                if ($orderDetail = $unorderedItems->firstWhere('item_id', $favoriteItem->item->id)) {
                    $favoriteItem->unorderedVolume = $orderDetail->volume;
                } else {
                    $favoriteItem->unorderedVolume = 1;
                }
            }

            return view('customer.pages.favorite', compact(
                'categories',
                'favoriteItems',
                'unorderedItems'
            ));
        } catch (\Exception $e) {
            Log::error('Error in favorite index: ' . $e->getMessage());
            return back()->with('error', 'お気に入り商品の取得に失敗しました。');
        }
    }
}
