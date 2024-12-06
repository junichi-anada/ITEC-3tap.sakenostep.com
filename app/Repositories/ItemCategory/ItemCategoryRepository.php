<?php

declare(strict_types=1);

namespace App\Repositories\ItemCategory;

use App\Models\ItemCategory;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * 商品カテゴリのリポジトリクラス
 *
 * 主な仕様:
 * - 商品カテゴリのCRUD操作を管理
 * - トランザクション制御による整合性の保証
 * - 柔軟な検索条件とページネーション機能の提供
 * - リレーションを考慮した動的な検索条件の適用
 *
 * 制限事項:
 * - トランザクション制御はTransactionServiceに依存
 * - 削除済みデータの取り扱いは明示的な指定が必要
 */
final class ItemCategoryRepository
{
    /**
     * @var TransactionService トランザクション制御用サービス
     */
    private TransactionService $transactionService;

    /**
     * コンストラクタ
     *
     * @param TransactionService $transactionService トランザクションサービス
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * 商品カテゴリを作成する
     *
     * @param array<string, mixed> $data 商品カテゴリデータ
     * @return ItemCategory 作成された商品カテゴリ
     * @throws \Exception データベース操作に失敗した場合
     */
    public function create(array $data): ItemCategory
    {
        return $this->transactionService->executeInTransaction(
            fn() => ItemCategory::create($data)
        );
    }

    /**
     * 商品カテゴリを取得する
     *
     * @param int $id 商品カテゴリID
     * @return ItemCategory|null 商品カテゴリ
     */
    public function find(int $id): ?ItemCategory
    {
        return ItemCategory::find($id);
    }

    /**
     * すべての商品カテゴリを取得する
     *
     * @return Collection 商品カテゴリのコレクション
     */
    public function getAll(): Collection
    {
        return ItemCategory::all();
    }

    /**
     * カテゴリコードとサイトIDでカテゴリを検索する
     *
     * @param string $categoryCode カテゴリコード
     * @param int $siteId サイトID
     * @return ItemCategory|null 商品カテゴリ
     */
    public function findByCategoryCode(string $categoryCode, int $siteId): ?ItemCategory
    {
        return ItemCategory::where('category_code', $categoryCode)
            ->where('site_id', $siteId)
            ->first();
    }

    /**
     * 商品カテゴリを更新する
     *
     * @param int $id 商品カテゴリID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function update(int $id, array $data): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id, $data) {
            $itemCategory = $this->find($id);
            return $itemCategory ? $itemCategory->update($data) : false;
        });
    }

    /**
     * 商品カテゴリを削除する
     *
     * @param int $id 商品カテゴリID
     * @return bool 削除が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function delete(int $id): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id) {
            $itemCategory = $this->find($id);
            return $itemCategory ? $itemCategory->delete() : false;
        });
    }

    /**
     * 指定された条件で商品カテゴリを検索する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Collection 商品カテゴリのコレクション
     */
    public function findBy(
        array $conditions = [],
        array $with = [],
        array $orderBy = [],
        bool $containTrash = false
    ): Collection {
        $query = $this->buildBaseQuery($conditions, $with, $orderBy, $containTrash);
        return $query->get();
    }

    /**
     * 指定された条件で商品カテゴリをページネーションで取得する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param int $perPage ページあたりの件数
     * @param array<string, string> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return LengthAwarePaginator ページネーション結果
     */
    public function findWithPagination(
        array $conditions = [],
        int $perPage = 15,
        array $with = [],
        array $orderBy = [],
        bool $containTrash = false
    ): LengthAwarePaginator {
        $query = $this->buildBaseQuery($conditions, $with, $orderBy, $containTrash);
        return $query->paginate($perPage);
    }

    /**
     * クエリビルダーの基本設定を行う
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Builder クエリビルダーインスタンス
     */
    private function buildBaseQuery(
        array $conditions = [],
        array $with = [],
        array $orderBy = [],
        bool $containTrash = false
    ): Builder {
        $query = ItemCategory::query();

        if (!empty($with)) {
            $query->with($with);
        }

        $this->applyConditions($query, $conditions);
        $this->applyOrderBy($query, $orderBy);

        if ($containTrash) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * 検索条件を適用する
     *
     * @param Builder $query クエリビルダー
     * @param array<string, mixed> $conditions 検索条件
     * @return void
     */
    private function applyConditions(Builder $query, array $conditions): void
    {
        foreach ($conditions as $field => $value) {
            if (strpos($field, '.') !== false) {
                $this->applyRelationCondition($query, $field, $value);
            } else {
                $this->applyDirectCondition($query, $field, $value);
            }
        }
    }

    /**
     * リレーション条件を適用する
     *
     * @param Builder $query クエリビルダー
     * @param string $field フィールド名
     * @param mixed $value 条件値
     * @return void
     */
    private function applyRelationCondition(Builder $query, string $field, mixed $value): void
    {
        [$relation, $relationField] = explode('.', $field, 2);
        $query->whereHas($relation, function ($subQuery) use ($relationField, $value) {
            $this->applyDirectCondition($subQuery, $relationField, $value);
        });
    }

    /**
     * 直接の条件を適用する
     *
     * @param Builder $query クエリビルダー
     * @param string $field フィールド名
     * @param mixed $value 条件値
     * @return void
     */
    private function applyDirectCondition(Builder $query, string $field, mixed $value): void
    {
        if (is_array($value)) {
            $query->whereIn($field, $value);
        } else {
            $query->where($field, $value);
        }
    }

    /**
     * ソート条件を適用する
     *
     * @param Builder $query クエリビルダー
     * @param array<string, string> $orderBy ソート条件
     * @return void
     */
    private function applyOrderBy(Builder $query, array $orderBy): void
    {
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
    }
}
