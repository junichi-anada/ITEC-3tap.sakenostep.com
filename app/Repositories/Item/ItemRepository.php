<?php

declare(strict_types=1);

namespace App\Repositories\Item;

use App\Models\Item;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * 商品のリポジトリクラス
 *
 * 主な仕様:
 * - 商品のCRUD操作を管理
 * - トランザクション制御による整合性の保証
 * - 柔軟な検索条件とページネーション機能の提供
 * - キーワード横断検索機能
 * - イーガーローディングによる効率的なデータ取得
 *
 * 制限事項:
 * - トランザクション制御はTransactionServiceに依存
 * - 削除済みデータの取り扱いは明示的な指定が必要
 * - 大量データの一括処理時はメモリ使用量に注意
 */
final class ItemRepository
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
     * 商品を作成する
     *
     * @param array<string, mixed> $data 商品データ
     * @return Item 作成された商品
     * @throws \Exception データベース操作に失敗した場合
     */
    public function create(array $data): Item
    {
        return $this->transactionService->executeInTransaction(
            fn() => Item::create($data)
        );
    }

    /**
     * 商品を取得する
     *
     * @param int $id 商品ID
     * @return Item|null 商品
     */
    public function find(int $id): ?Item
    {
        return Item::find($id);
    }

    /**
     * 商品を取得する（存在しない場合は例外をスロー）
     *
     * @param int $id 商品ID
     * @return Item 商品
     * @throws ModelNotFoundException 商品が存在しない場合
     */
    public function findOrFail(int $id): Item
    {
        return Item::findOrFail($id);
    }

    /**
     * すべての商品を取得する
     *
     * @param array<string, string> $with イーガーロード設定
     * @return Collection 商品のコレクション
     */
    public function getAll(array $with = []): Collection
    {
        $query = Item::query();
        if (!empty($with)) {
            $query->with($with);
        }
        return $query->get();
    }

    /**
     * 商品を更新する
     *
     * @param int $id 商品ID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws ModelNotFoundException 商品が存在しない場合
     * @throws \Exception データベース操作に失敗した場合
     */
    public function update(int $id, array $data): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id, $data) {
            $item = $this->findOrFail($id);
            return $item->update($data);
        });
    }

    /**
     * 商品を削除する
     *
     * @param int $id 商品ID
     * @return bool 削除が成功したかどうか
     * @throws ModelNotFoundException 商品が存在しない場合
     * @throws \Exception データベース操作に失敗した場合
     */
    public function delete(int $id): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id) {
            $item = $this->findOrFail($id);
            return $item->delete();
        });
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
        $query = Item::query();

        if (!empty($with)) {
            $query->with($with);
        }

        if ($containTrash) {
            $query->withTrashed();
        }

        $this->applyConditions($query, $conditions);
        $this->applyOrderBy($query, $orderBy);

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
            str_contains($field, '.')
                ? $this->applyRelationCondition($query, $field, $value)
                : $this->applyDirectCondition($query, $field, $value);
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
        is_array($value)
            ? $query->whereIn($field, $value)
            : $query->where($field, $value);
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

    /**
     * 指定された条件で商品を検索する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Collection 商品のコレクション
     */
    public function findBy(
        array $conditions,
        array $with = [],
        array $orderBy = [],
        bool $containTrash = false
    ): Collection {
        return $this->buildBaseQuery($conditions, $with, $orderBy, $containTrash)->get();
    }

    /**
     * 指定された条件で商品をページネーションで取得する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param int $perPage ページあたりの件数
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @return LengthAwarePaginator ページネーション結果
     */
    public function findWithPagination(
        array $conditions,
        int $perPage = 15,
        array $with = [],
        array $orderBy = []
    ): LengthAwarePaginator {
        return $this->buildBaseQuery($conditions, $with, $orderBy)->paginate($perPage);
    }

    /**
     * キーワードによる横断検索を行う
     *
     * @param array<string, mixed> $searchFields 検索対象フィールド
     * @param string $keyword 検索キーワード
     * @param array<string, mixed> $conditions 追加条件
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @return Collection 検索結果
     */
    public function searchByKeyword(
        array $searchFields,
        string $keyword,
        array $conditions = [],
        array $with = [],
        array $orderBy = ['created_at' => 'desc']
    ): Collection {
        $query = $this->buildBaseQuery($conditions, $with, $orderBy);

        $query->where(function ($query) use ($searchFields, $keyword) {
            foreach ($searchFields as $field) {
                str_contains($field, '.')
                    ? $this->applyKeywordRelationSearch($query, $field, $keyword)
                    : $query->orWhere($field, 'LIKE', "%{$keyword}%");
            }
        });

        return $query->get();
    }

    /**
     * キーワードのリレーション検索を適用する
     *
     * @param Builder $query クエリビルダー
     * @param string $field フィールド名
     * @param string $keyword 検索キーワード
     * @return void
     */
    private function applyKeywordRelationSearch(Builder $query, string $field, string $keyword): void
    {
        [$relation, $relationField] = explode('.', $field, 2);
        $query->orWhereHas($relation, function ($subQuery) use ($relationField, $keyword) {
            $subQuery->where($relationField, 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * 指定された条件で商品を1件検索して返す
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @return Item|null 検索結果の商品
     */
    public function findByOne(
        array $conditions,
        array $with = [],
        array $orderBy = ['created_at' => 'desc']
    ): ?Item {
        return $this->buildBaseQuery($conditions, $with, $orderBy)->first();
    }
}
