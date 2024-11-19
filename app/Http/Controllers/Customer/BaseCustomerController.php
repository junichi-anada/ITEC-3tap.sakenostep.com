<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Services\ItemCategory\Customer\ReadService as ItemCategoryReadService;
use Illuminate\View\View;

/**
 * 顧客向け基底コントローラー
 */
abstract class BaseCustomerController extends BaseController
{
    protected ItemCategoryReadService $itemCategoryReadService;

    public function __construct(ItemCategoryReadService $itemCategoryReadService)
    {
        $this->itemCategoryReadService = $itemCategoryReadService;
    }

    /**
     * カテゴリー一覧を取得
     *
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCategories(int $siteId)
    {
        return $this->itemCategoryReadService->getListBySiteId($siteId);
    }

    /**
     * ビューをレンダリング
     *
     * @param string $view
     * @param array $data
     * @return View
     */
    protected function renderView(string $view, array $data = []): View
    {
        $auth = $this->getAuthUser();
        $categories = $this->getCategories($auth->site_id);

        return view($view, array_merge(compact('categories'), $data));
    }
}
