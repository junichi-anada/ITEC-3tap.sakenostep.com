<?php

declare(strict_types=1);

namespace App\Repositories\OrderDetail;

use App\Models\OrderDetail;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * 注文詳細のリポジトリクラス
 *
 * 主な仕様:
 * - 注文詳細のCRUD操作を提供
 * - トランザクション制御による整合性の保証
 * - 柔軟な検索条件とページネーション機能の提供
 * - リレーションを考慮した動的な検索条件の適用
 *
 * 制限事項:
 * - トランザクション制御はTransactionServiceに依存
 * - 削除済みデータの取り扱いは明示的な指定が必要
 */
final class OrderDetailRepository
{
    /**
     * @var TransactionService トランザクション制御用サービス
     */
    private TransactionService $transactionService;

    /**
     * @var OrderDetail
     */
    private OrderDetail $model;

    /**
     * コンストラクタ
     *
     * @param TransactionService $transactionService トランザクションサービス
     * @param OrderDetail $model
     */
    public function __construct(TransactionService $transactionService, OrderDetail $model)
    {
        $this->transactionService = $transactionService;
        $this->model = $model;
    }

    /**
     * 注文詳細を作成する
     *
     * @param array<string, mixed> $data 注文詳細データ
     * @return OrderDetail 作成された注文詳細
     * @throws \Exception データベース操作に失敗した場合
     */
    public function create(array $data): OrderDetail
    {
        return $this->transactionService->executeInTransaction(
            fn() => OrderDetail::create($data)
        );
    }

    /**
     * 注文詳細を取得する
     *
     * @param int $id 注文詳細ID
     * @return OrderDetail|null 注文詳細
     */
    public function find(int $id): ?OrderDetail
    {
        return OrderDetail::find($id);
    }

    /**
     * すべての注文詳細を取得する
     *
     * @return Collection 注文詳細のコレクション
     */
    public function getAll(): Collection
    {
        return OrderDetail::all();
    }

    /**
     * 注文詳細を更新する
     *
     * @param int $id 注文詳細ID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function update(int $id, array $data): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id, $data) {
            $orderDetail = $this->find($id);
            return $orderDetail ? $orderDetail->update($data) : false;
        });
    }

    /**
     * 注文詳細を削除する
     *
     * @param int $id 注文詳細ID
     * @return bool 削除が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function delete(int $id): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id) {
            $orderDetail = $this->find($id);
            return $orderDetail ? $orderDetail->delete() : false;
        });
    }

    /**
     * 指定された条件で注文詳細を検索する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Collection 注文詳細のコレクション
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
     * 指定された条件で注文詳細をページネーションで取得する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param int $perPage ページあたりの件数
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @return LengthAwarePaginator ページネーション結果
     */
    public function findWithPagination(
        array $conditions = [],
        int $perPage = 15,
        array $with = [],
        array $orderBy = []
    ): LengthAwarePaginator {
        $query = $this->buildBaseQuery($conditions, $with, $orderBy);
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
        $query = OrderDetail::query();

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

    /**
     * 注文から商品を削除する
     *
     * @param int $orderId 注文ID
     * @param int $itemId 商品ID
     * @return bool 削除が成功したかどうか
     */
    public function deleteItemFromOrder(int $orderId, int $itemId): bool
    {
        return (bool) $this->model
            ->where('order_id', $orderId)
            ->where('item_id', $itemId)
            ->delete();
    }

    /**
     * 注文IDに紐づく注文詳細を全て削除する
     *
     * @param int $orderId 注文ID
     * @return bool 削除が成功したかどうか
     */
    public function deleteAllByOrderId(int $orderId): bool
    {
        return (bool) $this->model->where('order_id', $orderId)->delete();
    }
}

