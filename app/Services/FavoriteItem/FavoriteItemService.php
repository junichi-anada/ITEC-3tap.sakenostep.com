<?php

declare(strict_types=1);

namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Models\Item;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * お気に入り商品管理サービスクラス
 */
final class FavoriteItemService
{
    /**
     * お気に入り商品を登録する
     *
     * @param string $itemCode 商品コード
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @return array
     * @throws ValidationException
     */
    public function addToFavorites(string $itemCode, int $userId, int $siteId): array
    {
        try {
            DB::beginTransaction();

            // 商品の存在確認
            $item = Item::where('item_code', $itemCode)
                       ->where('site_id', $siteId)
                       ->first();

            if (!$item) {
                throw ValidationException::withMessages([
                    'item_code' => '指定された商品が見つかりません。'
                ]);
            }

            // 既存のお���に入り確認（ソフトデリート含む）
            $existingFavorite = FavoriteItem::withTrashed()
                ->where('user_id', $userId)
                ->where('item_id', $item->id)
                ->where('site_id', $siteId)
                ->first();

            if ($existingFavorite) {
                if ($existingFavorite->trashed()) {
                    $existingFavorite->restore();
                    DB::commit();
                    return ['message' => 'お気に入りに追加しました', 'data' => ['item_code' => $itemCode]];
                }
                throw ValidationException::withMessages([
                    'item_code' => '既にお気に入りに登録されています。'
                ]);
            }

            // 新規登録
            FavoriteItem::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'site_id' => $siteId
            ]);

            DB::commit();
            return ['message' => 'お気に入りに追加しました', 'data' => ['item_code' => $itemCode]];

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Favorite item validation error', [
                'item_code' => $itemCode,
                'user_id' => $userId,
                'site_id' => $siteId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Favorite item registration error', [
                'item_code' => $itemCode,
                'user_id' => $userId,
                'site_id' => $siteId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * お気に入り商品を削除する
     *
     * @param string $itemCode 商品コード
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @return array
     * @throws ValidationException
     */
    public function removeFromFavorites(string $itemCode, int $userId, int $siteId): array
    {
        try {
            DB::beginTransaction();

            $item = Item::where('item_code', $itemCode)
                       ->where('site_id', $siteId)
                       ->first();

            if (!$item) {
                throw ValidationException::withMessages([
                    'item_code' => '指定された商品が見つかりません。'
                ]);
            }

            $favorite = FavoriteItem::where('user_id', $userId)
                                  ->where('item_id', $item->id)
                                  ->where('site_id', $siteId)
                                  ->first();

            if (!$favorite) {
                throw ValidationException::withMessages([
                    'item_code' => 'お気に入りに登録されていません。'
                ]);
            }

            $favorite->delete();

            DB::commit();
            return ['message' => 'お気に入りから削除しました', 'data' => ['item_code' => $itemCode]];

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Favorite item removal validation error', [
                'item_code' => $itemCode,
                'user_id' => $userId,
                'site_id' => $siteId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Favorite item removal error', [
                'item_code' => $itemCode,
                'user_id' => $userId,
                'site_id' => $siteId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
