<?php

declare(strict_types=1);

namespace App\Services\Item;

use App\Models\Item;
use App\Repositories\Item\ItemRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * 商品サービスクラス
 *
 * 商品に関する操作を提供するサービスクラスです。
 * ItemRepositoryを利用して商品データの取得や操作を行います。
 */
final class ItemService
{
    /**
     * @var ItemRepository
     */
    private ItemRepository $itemRepository;

    /**
     * コンストラクタ
     *
     * @param ItemRepository $itemRepository 商品リポジトリ
     */
    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper($callback, $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error($errorMessage . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * カテゴリコードに基づいて商品一覧を取得
     *
     * @param string $categoryCode カテゴリコード
     * @param array<string, string> $orderBy ソート条件 ['column' => 'asc|desc']
     * @param array<string, string> $with イーガーロードするリレーション
     * @return Collection|null 商品コレクション
     */
    public function getItemsByCategory(string $categoryCode, array $orderBy = ['created_at' => 'desc'], array $with = []): ?Collection
    {
        return $this->tryCatchWrapper(
            function () use ($categoryCode, $orderBy, $with) {
                $conditions = [
                    'category_code' => $categoryCode
                ];

                return $this->itemRepository->findBy(conditions: $conditions, with: $with, orderBy: $orderBy);
            },
            'カテゴリコードに基づく商品一覧の取得に失敗しました'
        );
    }

    /**
     * サイトIDとカテゴリIDを基に商品一覧を取得
     *
     * @param int $siteId サイトID
     * @param int $categoryId カテゴリID
     * @return Collection|null 商品コレクション
     */
    public function getListBySiteIdAndCategoryId(int $siteId, int $categoryId): ?Collection
    {
        return $this->tryCatchWrapper(
            function () use ($siteId, $categoryId) {
                $conditions = [
                    'site_id' => $siteId,
                    'category_id' => $categoryId,
                    // 'published_at' => true
                ];

                return $this->itemRepository->findBy(
                    conditions: $conditions,
                    orderBy: ['created_at' => 'desc']
                );
            },
            'サイトIDとカテゴリIDに基づく商品一覧の取得に失敗しました',
            ['site_id' => $siteId, 'category_id' => $categoryId]
        );
    }

    /**
     * サイトIDに基づいておすすめ商品一覧を取得
     *
     * @param int $siteId サイトID
     * @param array<string, string> $orderBy ソート条件 ['column' => 'asc|desc']
     * @param array<string, string> $with イーガーロードするリレーション
     * @return Collection|null おすすめ商品コレクション
     */
    public function getRecommendedItems(int $siteId, array $orderBy = ['created_at' => 'desc'], array $with = []): ?Collection
    {
        return $this->tryCatchWrapper(
            function () use ($siteId, $orderBy, $with) {
                $conditions = [
                    'site_id' => $siteId,
                    'is_recommended' => true,
                ];

                return $this->itemRepository->findBy(
                    conditions: $conditions,
                    with: $with,
                    orderBy: $orderBy
                );
            },
            'サイトIDに基づくおすすめ商品一覧の取得に失敗しました',
            ['site_id' => $siteId]
        );
    }

    /**
     * キーワードによる商品検索を実行
     *
     * @param string $keyword 検索キーワード
     * @param int $siteId サイトID
     * @param array<string, mixed> $conditions 追加の検索条件
     * @param array<string, string> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件 ['column' => 'asc|desc']
     * @return \Illuminate\Database\Eloquent\Collection|null 検索結果の商品コレクション
     */
    public function searchByKeyword(
        string $keyword,
        int $siteId,
        array $conditions = [],
        array $with = [],
        array $orderBy = ['created_at' => 'desc']
    ): ?\Illuminate\Database\Eloquent\Collection {
        return $this->tryCatchWrapper(
            function () use ($keyword, $siteId, $conditions, $with, $orderBy) {
                // 検索対象のフィールドを指定
                $searchFields = [
                    'name',
                    'item_code'
                ];

                // サイトIDを検索条件に追加
                $conditions['site_id'] = $siteId;

                return $this->itemRepository->searchByKeyword(
                    searchFields: $searchFields,
                    keyword: $keyword,
                    conditions: $conditions,
                    with: $with,
                    orderBy: $orderBy
                );
            },
            'キーワードによる商品検索に失敗しました',
            ['keyword' => $keyword, 'site_id' => $siteId]
        );
    }

    /**
     * 商品コードとサイトIDで商品を1件検索する
     *
     * @param string $itemCode 商品コード
     * @param int $siteId サイトID
     * @return \App\Models\Item|null 検索結果の商品モデル
     */
    public function getByCodeOne(string $itemCode, int $siteId): ?Item
    {
        return $this->tryCatchWrapper(
            function () use ($itemCode, $siteId) {
                return $this->itemRepository->findByOne([
                    'item_code' => $itemCode,
                    'site_id' => $siteId
                ]);
            },
            '商品の取得に失敗しました'
        );
    }

}
