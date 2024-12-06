<?php
/**
 * 商品カテゴリの検索サービス
 *
 * 主な仕様:
 * - カテゴリ情報の検索機能を提供
 * - サイトIDとカテゴリコードによる検索
 * - カテゴリIDによる検索
 * - 親カテゴリに基づくサブカテゴリの検索
 *
 * 制限事項:
 * - 検索操作のみを扱います
 * - データの更新は行いません
 */
namespace App\Services\ItemCategory;

use App\Models\ItemCategory;
use App\Repositories\ItemCategory\ItemCategorySearchRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final class ItemCategorySearchService
{
    /**
     * @var ItemCategorySearchRepository
     */
    private ItemCategorySearchRepository $itemCategorySearchRepository;

    /**
     * コンストラクタ
     *
     * @param ItemCategorySearchRepository $itemCategorySearchRepository
     */
    public function __construct(ItemCategorySearchRepository $itemCategorySearchRepository)
    {
        $this->itemCategorySearchRepository = $itemCategorySearchRepository;
    }

}
