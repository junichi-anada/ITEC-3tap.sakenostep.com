<?php

declare(strict_types=1);

/**
 * 商品カテゴリサービス
 *
 * 商品カテゴリの操作に関する基本的なビジネスロジックを提供します。
 * カテゴリの作成、更新、削除、および検索機能を担当します。
 *
 * 主な仕様:
 * - カテゴリのCRUD操作を提供
 * - サイトごとのカテゴリ管理
 * - 公開/非公開状態の管理
 * - パンくずリストの生成
 * - 検索機能の提供
 *
 * 制限事項:
 * - データベースアクセスは全てリポジトリクラスを経由
 * - トランザクション制御はリポジトリに委譲
 *
 * @package App\Services\ItemCategory
 * @version 1.3
 */

namespace App\Services\ItemCategory;

use App\Models\ItemCategory;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final class ItemCategoryService
{
    /**
     * @var ItemCategoryRepository カテゴリリポジトリ
     */
    private ItemCategoryRepository $itemCategoryRepository;

    /**
     * コンストラクタ
     *
     * @param ItemCategoryRepository $itemCategoryRepository カテゴリリポジトリ
     */
    public function __construct(
        ItemCategoryRepository $itemCategoryRepository
    ) {
        $this->itemCategoryRepository = $itemCategoryRepository;
    }

    /**
     * 例外処理を共通化するためのラッパーメソッド
     *
     * @template T
     * @param \Closure(): T $callback 実行する処理
     * @param string $errorMessage エラーメッセージ
     * @param array<string, mixed> $context エラーコンテキスト
     * @return T|null
     */
    private function tryCatchWrapper(
        \Closure $callback,
        string $errorMessage,
        array $context = []
    ): mixed {
        try {
            return $callback();
        } catch (\Exception $e) {
            $logContext = array_merge([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], $context);

            Log::error("Error in ItemCategoryService: $errorMessage", $logContext);
            return null;
        }
    }

    /**
     * カテゴリを作成
     *
     * @param array<string, mixed> $data カテゴリデータ
     * @return ItemCategory|null 作成されたカテゴリ、失敗時はnull
     */
    public function create(array $data): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->create($data),
            'カテゴリの作成に失敗しました',
            ['data' => $data]
        );
    }

    /**
     * カテゴリを更新
     *
     * @param int $id カテゴリID
     * @param array<string, mixed> $data 更新データ
     * @return ItemCategory|null 更新されたカテゴリ、失敗時はnull
     */
    public function update(int $id, array $data): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->update($id, $data),
            'カテゴリの更新に失敗しました',
            ['id' => $id, 'data' => $data]
        );
    }

    /**
     * カテゴリを削除
     *
     * @param int $id カテゴリID
     * @return bool|null 削除成功時はtrue、失敗時はnull
     */
    public function delete(int $id): ?bool
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->delete($id),
            'カテゴリの削除に失敗しました',
            ['id' => $id]
        );
    }

    /**
     * IDでカテゴリを取得
     *
     * @param int $id カテゴリID
     * @return ItemCategory|null 取得したカテゴリ、存在しない場合はnull
     */
    public function findById(int $id): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findById($id),
            'カテゴリの取得に失敗しました',
            ['id' => $id]
        );
    }

    /**
     * サイトIDに基づくカテゴリ一覧を取得
     *
     * @param int $siteId サイトID
     * @return Collection|null カテゴリ一覧、失敗時はnull
     */
    public function getListBySiteId(int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findBy(['site_id' => $siteId]),
            'サイトのカテゴリ一覧の取得に失敗しました',
            ['site_id' => $siteId]
        );
    }

    /**
     * サイトの全カテゴリ一覧を取得
     *
     * @param int $siteId サイトID
     * @return Collection|null カテゴリ一覧、失敗時はnull
     */
    public function getAllCategories(int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findBy(['site_id' => $siteId]),
            'カテゴリ一覧の取得に失敗しました',
            ['site_id' => $siteId]
        );
    }

    /**
     * 公開状態のカテゴリのみを取得
     *
     * @param int $siteId サイトID
     * @return Collection|null 公開状態のカテゴリ一覧、失敗時はnull
     */
    public function getPublishedCategories(int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findBy([
                'site_id' => $siteId,
                'is_published' => true,
            ]),
            '公開カテゴリ一覧の取得に失敗しました',
            ['site_id' => $siteId]
        );
    }

    /**
     * カテゴリのパンくずリストを取得
     *
     * @param int $categoryId カテゴリID
     * @param int $siteId サイトID
     * @return Collection|null パンくずリスト、失敗時はnull
     */
    public function getCategoryBreadcrumbs(int $categoryId, int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->getBreadcrumbs($categoryId, $siteId),
            'カテゴリパンくずリストの取得に失敗しました',
            ['category_id' => $categoryId, 'site_id' => $siteId]
        );
    }

    /**
     * サイトIDとカテゴリコードでカテゴリを検索する
     *
     * @param int $siteId サイトID
     * @param string $categoryCode カテゴリコード
     * @return ItemCategory|null
     */
    public function getByCategoryCode(int $siteId, string $categoryCode): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findByCategoryCode($categoryCode, $siteId),
            'カテゴリコードによる検索に失敗しました',
            ['site_id' => $siteId, 'category_code' => $categoryCode]
        );
    }

    /**
     * カテゴリコードでカテゴリを検索する
     *
     * @param string $categoryCode カテゴリコード
     * @return ItemCategory|null
     */
    public function getByCode(string $categoryCode): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findBy(['category_code' => $categoryCode])->first(),
            'カテゴリコードによる検索に失敗しました',
            ['category_code' => $categoryCode]
        );
    }

    /**
     * サイトIDに基づくカテゴリ一覧を取得する
     *
     * @param int $siteId サイトID
     * @return Collection|null
     */
    public function getBySiteId(int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findBy(['site_id' => $siteId]),
            'サイトIDによるカテゴリ検索に失敗しました',
            ['site_id' => $siteId]
        );
    }

    /**
     * 親カテゴリに基づくサブカテゴリを取得する
     *
     * @param int $parentId 親カテゴリID
     * @param int $siteId サイトID
     * @return Collection|null
     */
    public function getSubCategories(int $parentId, int $siteId): ?Collection
    {
        return $this->tryCatchWrapper(
            fn() => $this->itemCategoryRepository->findSubCategories($parentId, $siteId),
            'サブカテゴリの取得に失敗しました',
            ['parent_id' => $parentId, 'site_id' => $siteId]
        );
    }
}
