<?php
/**
 * 商品カテゴリの一覧取得サービス
 *
 * 商品カテゴリの一覧を取得するためのサービスクラスです。
 * サイトIDに基づいて、カテゴリの階層構造や表示順を考慮した一覧を提供します。
 */
namespace App\Services\ItemCategory;

use App\Models\ItemCategory;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final class ItemCategoryListService
{
    /**
     * @var ItemCategoryRepository
     */
    private ItemCategoryRepository $itemCategoryRepository;

    /**
     * コンストラクタ
     *
     * @param ItemCategoryRepository $itemCategoryRepository
     */
    public function __construct(ItemCategoryRepository $itemCategoryRepository)
    {
        $this->itemCategoryRepository = $itemCategoryRepository;
    }

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("Error: $errorMessage - " . $e->getMessage());
            return null;
        }
    }

    /**
     * 指定されたサイトの全カテゴリ一覧を取得する
     *
     * @param int $siteId サイトID
     * @return Collection|null カテゴリ一覧
     */
    public function getAllCategories(int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            function () use ($siteId) {

                return $this->itemCategoryRepository->findBy(['site_id' => $siteId]);
            },
            'カテゴリ一覧の取得に失敗しました'
        );
    }

    /**
     * 公開状態のカテゴリのみを取得する
     *
     * @param int $siteId サイトID
     * @return Collection|null 公開状態のカテゴリ一覧
     */
    public function getPublishedCategories(int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            function () use ($siteId) {
                $conditions = [
                    'site_id' => $siteId,
                    'is_published' => true,
                ];
                return $this->itemCategoryRepository->findBy($conditions);
            },
            '公開カテゴリ一覧の取得に失敗しました'
        );
    }

    /**
     * カテゴリのパンくずリストを取得する
     *
     * @param int $categoryId カテゴリID
     * @param int $siteId サイトID
     * @return Collection|null パンくずリスト
     */
    public function getCategoryBreadcrumbs(int $categoryId, int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            function () use ($categoryId, $siteId) {
                return $this->itemCategoryRepository->getBreadcrumbs($categoryId, $siteId);
            },
            'カテゴリパンくずリストの取得に失敗しました'
        );
    }
}
