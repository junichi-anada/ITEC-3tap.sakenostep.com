<?php
/**
 * Webコントローラ
 * 顧客向け商品カテゴリ機能
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\Item\Customer\ReadService as ItemReadService;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use App\Services\FavoriteItem\Customer\ReadService as FavoriteItemReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    protected $itemReadService;
    protected $itemCategoryReadService;
    protected $favoriteItemReadService;
    protected $orderDetailReadService;

    /**
     * コンストラクタ
     *
     * @param ItemReadService $itemReadService
     * @param ItemCategoryReadService $itemCategoryReadService
     * @param FavoriteItemReadService $favoriteItemReadService
     * @param OrderDetailReadService $orderDetailReadService
     */
    public function __construct(
        ItemReadService $itemReadService,
        ItemCategoryReadService $itemCategoryReadService,
        FavoriteItemReadService $favoriteItemReadService,
        OrderDetailReadService $orderDetailReadService
    ) {
        $this->itemReadService = $itemReadService;
        $this->itemCategoryReadService = $itemCategoryReadService;
        $this->favoriteItemReadService = $favoriteItemReadService;
        $this->orderDetailReadService = $orderDetailReadService;
    }


    /**
     * index
     * カテゴリ一覧表示
     *
     * @return void
     */
    public function index()
    {
        $auth = Auth::user();

        // 該当サイトのカテゴリ一覧を取得
        $categories = $this->itemCategoryReadService->getListBySiteId($auth->site_id);

        if ($categories->isEmpty()) {
            $error = __('カテゴリが存在しません。');
            return view('customer.category', compact('error'));
        }

        return view('customer.category', compact('categories'));
    }

    /**
     * show
     * カテゴリに属する商品一覧表示
     * 1. カテゴリに所属する商品一覧を取得
     * 2. ログインしているサイトでのユーザーのお気に入り商品を取得
     * 3. ログインしているサイトでのユーザーのオーダーリストの商品を取得
     *
     * @param  $code
     * @return void
     */
    public function show($code)
    {
        $auth = Auth::user();

        $categories = $this->itemCategoryReadService->getListBySiteId($auth->site_id);
        $category = $this->itemCategoryReadService->getByCategoryCode($auth->site_id, $code);

        $items = $this->itemReadService->getListBySiteIdAndCategoryId($auth->site_id, $category->id)->toArray();
        $favoriteItems = $this->favoriteItemReadService->getItemIdListByUserAndSiteId($auth->id, $auth->site_id);
        $unorderedItems = $this->orderDetailReadService->getUnorderedListByUserIdAndSiteId($auth->id, $auth->site_id)->toArray();

        $items = $this->calculateItemScores($items, $favoriteItems, $unorderedItems);

        return view('customer.category_item', compact('items', 'category'));
    }

    private function calculateItemScores($items, $favoriteItems, $unorderedItems)
    {
        foreach ($items as $key => $item) {
            $items[$key]['score1'] = in_array($item['id'], $unorderedItems) ? 1 : 0;
            $items[$key]['score2'] = in_array($item['id'], $favoriteItems) ? 1 : 0;
            $items[$key]['unorderedVolume'] = in_array($item['id'], $unorderedItems) ? $unorderedItems['volume'] : 1;
        }
        return $items;
    }
}
