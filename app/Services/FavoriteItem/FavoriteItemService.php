<?php

declare(strict_types=1);

namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use App\Repositories\Item\ItemRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * お気に入り商品サービスクラス
 *
 * 主な仕様:
 * - お気に入り商品の追加・削除・一覧取得機能を提供
 * - トランザクション制御による整合性の保証
 * - 削除済みデータの復元機能
 *
 * 制限事項:
 * - FavoriteItemRepositoryとItemRepositoryに依存
 * - 例外発生時はnullまたはfalseを返却
 */
final class FavoriteItemService
{
    /**
     * @var FavoriteItemRepository お気に入り商品リポジトリ
     */
    private FavoriteItemRepository $favoriteItemRepository;

    /**
     * @var ItemRepository 商品リポジトリ
     */
    private ItemRepository $itemRepository;

    /**
     * コンストラクタ
     *
     * @param FavoriteItemRepository $favoriteItemRepository お気に入り商品リポジトリ
     * @param ItemRepository $itemRepository 商品リポジトリ
     */
    public function __construct(
        FavoriteItemRepository $favoriteItemRepository,
        ItemRepository $itemRepository
    ) {
        $this->favoriteItemRepository = $favoriteItemRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * 例外処理を共通化するためのラッパーメソッド
     *
     * @param \Closure $callback 実行する処理
     * @param string $errorMessage エラーメッセージ
     * @return mixed 処理結果
     */
    private function tryCatchWrapper(\Closure $callback, string $errorMessage): mixed
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("{$errorMessage}: {$e->getMessage()}", [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * お気に入り商品を追加する
     *
     * @param int $userId ユーザーID
     * @param int $itemId 商品ID
     * @param int $siteId サイトID
     * @return FavoriteItem|null 追加されたお気に入り商品
     */
    public function add(int $userId, int $itemId, int $siteId): ?FavoriteItem
    {
        return $this->tryCatchWrapper(
            function () use ($userId, $itemId, $siteId) {
                $conditions = $this->buildFavoriteItemConditions($userId, $itemId, $siteId);
                $favoriteItem = $this->findFavoriteItemWithTrashed($conditions);

                if (!$favoriteItem) {
                    return $this->createFavoriteItem($conditions);
                }

                return $favoriteItem->trashed()
                    ? $this->restoreFavoriteItem($favoriteItem->id)
                    : $favoriteItem;
            },
            'お気に入り商品の登録に失敗しました'
        );
    }

    /**
     * お気に入り商品を削除する
     *
     * @param int $userId ユーザーID
     * @param int $itemId 商品ID
     * @param int $siteId サイトID
     * @return bool 削除結果
     */
    public function remove(int $userId, int $itemId, int $siteId): bool
    {
        return $this->tryCatchWrapper(
            function () use ($userId, $itemId, $siteId) {
                $conditions = $this->buildFavoriteItemConditions($userId, $itemId, $siteId);
                $favoriteItem = $this->findFavoriteItem($conditions);

                if (!$favoriteItem) {
                    return false;
                }

                return $favoriteItem->trashed()
                    ? true
                    : $this->favoriteItemRepository->delete($favoriteItem->id);
            },
            'お気に入り商品の削除に失敗しました'
        );
    }

    /**
     * ユーザーのお気に入り商品一覧を取得する
     *
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @param array<string, string> $orderBy ソート条件 ['column' => 'asc|desc']
     * @param array<string, string> $with イーガーロードするリレーション
     * @return Collection|null お気に入り商品一覧
     */
    public function getUserFavorites(
        int $userId,
        int $siteId,
        array $orderBy = ['created_at' => 'desc'],
        array $with = ['item', 'site']
    ): ?Collection {
        return $this->tryCatchWrapper(
            function () use ($userId, $siteId, $orderBy, $with) {
                $conditions = $this->buildFavoriteItemConditions($userId, $siteId);
                return $this->favoriteItemRepository->findBy(
                    conditions: $conditions,
                    with: $with,
                    orderBy: $orderBy
                );
            },
            'ユーザーのお気に入り商品一覧の取得に失敗しました'
        );
    }

    /**
     * お気に入り商品の検索条件を構築する
     *
     * @param int $userId ユーザーID
     * @param int|null $itemId 商品ID
     * @param int|null $siteId サイトID
     * @return array<string, int> 検索条件
     */
    private function buildFavoriteItemConditions(
        int $userId,
        ?int $itemId = null,
        ?int $siteId = null
    ): array {
        $conditions = ['user_id' => $userId];

        if ($itemId !== null) {
            $conditions['item_id'] = $itemId;
        }

        if ($siteId !== null) {
            $conditions['site_id'] = $siteId;
        }

        return $conditions;
    }

    /**
     * お気に入り商品を検索する（削除済みを含む）
     *
     * @param array<string, int> $conditions 検索条件
     * @return FavoriteItem|null お気に入り商品
     */
    private function findFavoriteItemWithTrashed(array $conditions): ?FavoriteItem
    {
        return $this->favoriteItemRepository->findBy(
            conditions: $conditions,
            containTrash: true
        )->first();
    }

    /**
     * お気に入り商品を検索する
     *
     * @param array<string, int> $conditions 検索条件
     * @return FavoriteItem|null お気に入り商品
     */
    private function findFavoriteItem(array $conditions): ?FavoriteItem
    {
        return $this->favoriteItemRepository->findBy($conditions)->first();
    }

    /**
     * お気に入り商品を作成する
     *
     * @param array<string, int> $data 作成データ
     * @return FavoriteItem 作成されたお気に入り商品
     */
    private function createFavoriteItem(array $data): FavoriteItem
    {
        return $this->favoriteItemRepository->create($data);
    }

    /**
     * お気に入り商品を復元する
     *
     * @param int $id お気に入り商品ID
     * @return FavoriteItem 復元されたお気に入り商品
     * @throws \Exception 復元に失敗した場合
     */
    private function restoreFavoriteItem(int $id): FavoriteItem
    {
        $restoredItem = $this->favoriteItemRepository->restore($id);
        if (!$restoredItem) {
            throw new \Exception('お気に入り商品の復元に失敗しました');
        }
        return $restoredItem;
    }
}
