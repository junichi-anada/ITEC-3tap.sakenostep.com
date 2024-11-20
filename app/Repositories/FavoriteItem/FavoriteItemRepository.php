<?php

declare(strict_types=1);

namespace App\Repositories\FavoriteItem;

use App\Models\FavoriteItem;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * お気に入り商品のリポジトリクラス
 *
 * 主な仕様:
 * - お気に入り商品のCRUD操作を管理
 * - トランザクション制御による整合性の保証
 * - 柔軟な検索条件とページネーション機能の提供
 *
 * 制限事項:
 * - トランザクション制御はTransactionServiceに依存
 * - 削除済みデータの取り扱いは明示的な指定が必要
 */
final class FavoriteItemRepository
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
     * お気に入り商品を作成する
     *
     * @param array<string, mixed> $data お気に入り商品データ
     * @return FavoriteItem 作成されたお気に入り商品
     * @throws \Exception データベース操作に失敗した場合
     */
    public function create(array $data): FavoriteItem
    {
        return $this->transactionService->executeInTransaction(
            fn() => FavoriteItem::create($data)
        );
    }

    /**
     * お気に入り商品を取得する
     *
     * @param int $id お気に入り商品ID
     * @return FavoriteItem|null お気に入り商品
     */
    public function find(int $id): ?FavoriteItem
    {
        return FavoriteItem::find($id);
    }

    /**
     * すべてのお気に入り商品を取得する
     *
     * @return Collection お気に入り商品のコレクション
     */
    public function getAll(): Collection
    {
        return FavoriteItem::all();
    }

    /**
     * お気に入り商品を更新する
     *
     * @param int $id お気に入り商品ID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function update(int $id, array $data): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id, $data) {
            $favoriteItem = $this->find($id);
            return $favoriteItem ? $favoriteItem->update($data) : false;
        });
    }

    /**
     * お気に入り商品を削除する
     *
     * @param int $id お気に入り商品ID
     * @return bool 削除が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function delete(int $id): bool
    {
        $model = FavoriteItem::find($id);
        if (!$model) {
            return false;
        }

        return $model->delete();
    }

    /**
     * 削除済みのお気に入り商品を復元する
     *
     * @param int $id お気に入り商品ID
     * @return FavoriteItem|null 復元された商品、存在しない場合はnull
     */
    public function restore(int $id): ?FavoriteItem
    {
        return $this->transactionService->executeInTransaction(function () use ($id) {
            $model = FavoriteItem::withTrashed()->find($id);
            if (!$model) {
                return null;
            }
            $model->restore();
            return $model->fresh();
        });
    }

    /**
     * 指定された条件でお気に入り商品を検索する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Collection お気に入り商品のコレクション
     */
    public function findBy(
        array $conditions,
        array $with = [],
        array $orderBy = [],
        bool $containTrash = false
    ): Collection {
        $query = $this->buildQuery($conditions, $with, $orderBy, $containTrash);
        return $query->get();
    }

    /**
     * 指定された条件でお気に入り商品をページネーションで取得する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param int $perPage ページあたりの件数
     * @param array<string, string> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件
     * @return LengthAwarePaginator ページネーション結果
     */
    public function findWithPagination(
        array $conditions,
        int $perPage = 15,
        array $with = [],
        array $orderBy = []
    ): LengthAwarePaginator {
        $query = $this->buildQuery($conditions, $with, $orderBy);
        return $query->paginate($perPage);
    }

    /**
     * クエリビルダーを構築する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロードするリレーション
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Builder クエリビルダー
     */
    private function buildQuery(
        array $conditions,
        array $with = [],
        array $orderBy = [],
        bool $containTrash = false
    ): Builder {
        $query = FavoriteItem::query();

        if (!empty($with)) {
            $query->with($with);
        }

        foreach ($conditions as $field => $value) {
            $this->applyCondition($query, $field, $value);
        }

        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        if ($containTrash) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * 検索条件を適用する
     *
     * @param Builder $query クエリビルダー
     * @param string $field フィールド名
     * @param mixed $value 検索値
     */
    private function applyCondition(Builder $query, string $field, mixed $value): void
    {
        if (str_contains($field, '.')) {
            $this->applyRelationCondition($query, $field, $value);
        } else {
            $this->applyDirectCondition($query, $field, $value);
        }
    }

    /**
     * リレーション条件を適用する
     *
     * @param Builder $query クエリビルダー
     * @param string $field フィールド名
     * @param mixed $value 検索値
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
     * @param mixed $value 検索値
     */
    private function applyDirectCondition(Builder $query, string $field, mixed $value): void
    {
        is_array($value)
            ? $query->whereIn($field, $value)
            : $query->where($field, $value);
    }
}
