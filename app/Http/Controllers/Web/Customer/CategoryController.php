<?php
/**
 * 顧客向け商品カテゴリ機能のWebコントローラ
 *
 * 主な仕様:
 * - カテゴリ一覧の表示
 * - カテゴリに属する商品一覧の表示
 * - 商品のスコア計算機能
 *
 * 制限事項:
 * - 認証済みユーザーのみアクセス可能
 * - 各サイトごとのカテゴリ・商品のみ表示
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\ItemCategory\ItemCategoryService;
use App\Services\Item\ItemService;
use App\Services\FavoriteItem\FavoriteItemService;
use App\Services\Order\OrderService;
use App\Services\OrderDetail\OrderDetailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
final class CategoryController extends Controller
{
    /**
     * コンストラクタ
     *
     * @param ItemCategoryService $itemCategoryService カテゴリ一覧サービス
     * @param ItemService $itemService 商品読み取りサービス
     * @param FavoriteItemService $favoriteItemService お気に入り商品読み取りサービス
     * @param OrderService $orderService 注文読み取りサービス
     * @param OrderDetailService $orderDetailService 注文詳細読み取りサービス
     */
    public function __construct(
        private readonly ItemCategoryService $itemCategoryService,
        private readonly ItemService $itemService,
        private readonly FavoriteItemService $favoriteItemService,
        private readonly OrderService $orderService,
        private readonly OrderDetailService $orderDetailService
    ) {}

    /**
     * カテゴリ一覧を表示
     *
     * @return View
     */
    public function index(): View
    {
        $auth = Auth::user();

        // カテゴリ一覧
        $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

        if ($categories->isEmpty()) {
            return view('customer.category', [
                'error' => __('カテゴリが存在しません。')
            ]);
        }

        return view('customer.category', compact('categories'));
    }

    /**
     * カテゴリに属する商品一覧を表示
     *
     * @param string $categoryCode カテゴリコード
     * @return View
     */
    public function show(string $categoryCode): View
    {
        $auth = Auth::user();

        $recommendedItems = [];
        $favoriteItems = [];
        $unorderedItems = [];

        // カテゴリ一覧
        $categories = $this->itemCategoryService->getPublishedCategories($auth->site_id);

        // サイトIDとカテゴリIDを基に商品一覧を取得
        $category = $this->itemCategoryService->getByCode($categoryCode);
        if (!$category) {
            return view('customer.category_item', [
                'error' => __('カテゴリが存在しません。')
            ]);
        }
        $items = $this->itemService->getListBySiteIdAndCategoryId($auth->site_id, $category->id)->toArray();

        // 顧客のお気に入り商品ID一覧を取得
        $favoriteItems = $this->favoriteItemService->getUserFavorites( $auth->id, $auth->site_id )->toArray();
        if (!$favoriteItems) {
            $favoriteItems = [];
        }

        $unorderedOrder = $this->orderService->getLatestUnorderedOrderByUserAndSite($auth->id, $auth->site_id);
        if (!$unorderedOrder) {
            $unorderedItems = [];
        } else {
            $unorderedItems = $this->orderDetailService->getOrderDetailsByOrderId( $unorderedOrder->id )->toArray();
        }

        $items = $this->calculateItemScores($items, $favoriteItems, $unorderedItems);

        return view('customer.category_item', [
            'items' => $items,
            'category' => $category
        ]);
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
        }
        // 未注文商品が空の場合は、スコア1を0で返却
        if (empty($unorderedItems)) {
            foreach ($items as $key => $item) {
                $items[$key]['score1'] = 0;
                $items[$key]['unorderedVolume'] = 1;
            }
            return $items;
        }

        foreach ($items as $key => $item) {
            $items[$key]['score1'] = in_array($item['id'], array_column($unorderedItems, 'item_id')) ? 1 : 0;
            $items[$key]['score2'] = in_array($item['id'], array_column($favoriteItems, 'item_id')) ? 1 : 0;
            $items[$key]['unorderedVolume'] = 1;
        }

        return $items;
    }
}
