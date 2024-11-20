<?php
/**
 * 商品検索サービス
 *
 * 主な仕様:
 * - 商品情報の検索機能を提供
 * - サイトIDとカテゴリIDによる商品検索
 * - 商品IDによる単一商品検索
 * - キーワードによる商品検索
 * - おすすめ商品の取得
 *
 * 制限事項:
 * - 検索操作のみを扱います
 * - データの更新は行いません
 */
namespace App\Services\Item;

use App\Models\Item;
use App\Repositories\Item\ItemSearchRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

final class ItemSearchService
{
    /**
     * @var ItemSearchRepository
     */
    private ItemSearchRepository $itemSearchRepository;

    /**
     * コンストラクタ
     *
     * @param ItemSearchRepository $itemSearchRepository
     */
    public function __construct(ItemSearchRepository $itemSearchRepository)
    {
        $this->itemSearchRepository = $itemSearchRepository;
    }

    /**
     * サイトIDとカテゴリIDで商品を検索する
     *
     * @param int $siteId サイトID
     * @param int $categoryId カテゴリID
     * @return Collection|null
     */
    public function getListBySiteIdAndCategoryId(int $siteId, int $categoryId): ?Collection
    {
        try {
            return $this->itemSearchRepository->findByCategory($categoryId, $siteId);
        } catch (\Exception $e) {
            Log::error("ItemSearchService::getListBySiteIdAndCategoryId - サイトID: {$siteId}, カテゴリID: {$categoryId} - エラー: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * 商品IDで商品を検索する
     *
     * @param int $itemId 商品ID
     * @return Item|null
     */
    public function getById(int $itemId): ?Item
    {
        try {
            return $this->itemSearchRepository->findById($itemId);
        } catch (\Exception $e) {
            Log::error("ItemSearchService::getById - 商品ID: {$itemId} - エラー: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * 商品コードで商品を検索する
     *
     * @param string $itemCode 商品コード
     * @param int $siteId サイトID
     * @return Item|null
     */
    public function getByCode(string $itemCode, int $siteId): ?Item
    {
        try {
            return $this->itemSearchRepository->findByCode($itemCode, $siteId);
        } catch (\Exception $e) {
            Log::error("ItemSearchService::getByCode - 商品コード: {$itemCode}, サイトID: {$siteId} - エラー: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * キーワードで商品を検索する
     *
     * @param string $keyword 検索キーワード
     * @param int $siteId サイトID
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator|null
     */
    public function searchByKeyword(string $keyword, int $siteId, int $perPage = 15): ?LengthAwarePaginator
    {
        try {
            return $this->itemSearchRepository->searchByKeyword($keyword, $siteId, $perPage);
        } catch (\Exception $e) {
            Log::error("ItemSearchService::searchByKeyword - キーワード: {$keyword}, サイトID: {$siteId} - エラー: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * おすすめ商品を取得する
     *
     * @param int $siteId サイトID
     * @param int $limit 取得件数
     * @return Collection|null
     */
    public function getRecommendedItems(int $siteId, int $limit = 10): ?Collection
    {
        try {
            return $this->itemSearchRepository->findRecommendedItems($siteId, $limit);
        } catch (\Exception $e) {
            Log::error("ItemSearchService::getRecommendedItems - サイトID: {$siteId}, 取得件数: {$limit} - エラー: {$e->getMessage()}");
            return null;
        }
    }
}

