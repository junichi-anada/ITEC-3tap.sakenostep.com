<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order;
use App\Services\Transaction\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * 伝票のリポジトリクラス
 *
 * 主な仕様:
 * - 伝票のCRUD操作を提供
 * - トランザクション制御による整合性の保証
 * - 柔軟な検索条件とページネーション機能の提供
 * - リレーションを考慮した動的な検索条件の適用
 *
 * 制限事項:
 * - トランザクション制御はTransactionServiceに依存
 * - 削除済みデータの取り扱いは明示的な指定が必要
 */
final class OrderRepository
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
     * 伝票を作成する
     *
     * @param array<string, mixed> $data 伝票データ
     * @return Order 作成された伝票
     * @throws \Exception データベース操作に失敗した場合
     */
    public function create(array $data): Order
    {
        do {
            $orderCode = Str::ulid();
        } while (Order::where('order_code', $orderCode)->exists());

        $data['order_code'] = $orderCode;

        return $this->transactionService->executeInTransaction(
            fn() => Order::create($data)
        );
    }

    /**
     * 伝票を取得する
     *
     * @param int $id 伝票ID
     * @return Order|null 伝票
     */
    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * すべての伝票を取得する
     *
     * @return Collection 伝票のコレクション
     */
    public function getAll(): Collection
    {
        return Order::all();
    }

    /**
     * 伝票を更新する
     *
     * @param int $id 伝票ID
     * @param array<string, mixed> $data 更新データ
     * @return bool 更新が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function update(int $id, array $data): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id, $data) {
            $order = $this->find($id);
            return $order ? $order->update($data) : false;
        });
    }

    /**
     * 伝票を削除する
     *
     * @param int $id 伝票ID
     * @return bool 削除が成功したかどうか
     * @throws \Exception データベース操作に失敗した場合
     */
    public function delete(int $id): bool
    {
        return $this->transactionService->executeInTransaction(function () use ($id) {
            $order = $this->find($id);
            return $order ? $order->delete() : false;
        });
    }

    /**
     * 指定された条件で伝票を検索する
     *
     * @param array<string, mixed> $conditions 検索条件
     * @param array<string, string> $with イーガーロード設定
     * @param array<string, string> $orderBy ソート条件
     * @param bool $containTrash 削除済みデータを含めるか
     * @return Collection 伝票のコレクション
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
     * 指定された条件で伝票をページネーションで取得する
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
        $query = Order::query();

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
     * 注文日を更新する
     *
     * @param int $id 伝票ID
     * @return Order|null 更新された伝票、存在しない場合はnull
     * @throws \Exception データベース操作に失敗した場合
     */
    public function updateOrderDate(int $id): ?Order
    {
        return $this->transactionService->executeInTransaction(function () use ($id) {
            $order = $this->find($id);
            if (!$order) {
                return null;
            }

            $order->update(['ordered_at' => now()]);
            return $order->fresh();
        });
    }
}
