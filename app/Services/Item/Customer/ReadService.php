<?php
/**
 * 商品情報取得サービス
 * 
 * 商品の情報取得に関するサービスクラスです。
 */
namespace App\Services\Item\Customer;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class ReadService
{
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
     * サイトIDに基づいて商品リストを取得する
     *
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListBySiteId($siteId)
    {
        return $this->tryCatchWrapper(function () use ($siteId) {
            return Item::where('site_id', $siteId)->get();
        }, '商品リストの取得に失敗しました');
    }

    /**
     * サイトIDとカテゴリIDに基づいて商品リストを取得する
     *
     * @param int $siteId
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListBySiteIdAndCategoryId($siteId, $categoryId)
    {
        return $this->tryCatchWrapper(function () use ($siteId, $categoryId) {
            return Item::where('site_id', $siteId)
                      ->where('category_id', $categoryId)
                      ->get();
        }, '指定カテゴリの商品リストの取得に失敗しました');
    }

    /**
     * 指定されたIDを持つ商品情報を取得します。
     *
     * @param int $itemId
     * @return Item|null
     */
    public function getById($itemId)
    {
        return $this->tryCatchWrapper(function () use ($itemId) {
            return Item::find($itemId);
        }, '商品情報の取得に失敗しました');
    }

    /**
     * 指定された商品コードを持つ商品情報を取得します。
     *
     * @param string $itemCode
     * @return Item|null
     */
    public function getByItemCode($itemCode)
    {
        return $this->tryCatchWrapper(function () use ($itemCode) {
            return Item::where('item_code', $itemCode)->first();
        }, '商品コードによる商品情報の取得に失敗しました');
    }

    /**
     * 全ての商品情報を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getAll()
    {
        return $this->tryCatchWrapper(function () {
            return Item::all();
        }, '全商品情報の取得に失敗しました');
    }

    /**
     * おすすめ商品のリストを取得します。
     *
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getRecommendedItems($siteId)
    {
        return $this->tryCatchWrapper(function () use ($siteId) {
            return Item::where('site_id', $siteId)
                        ->where('is_recommended', true)
                        ->get();
        }, 'おすすめ商品リストの取得に失敗しました');
    }

    /**
     * 検索キーワードに基づいて商品リストを取得します。
     *
     * @param int $siteId
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function searchByKeyword($siteId, $keyword)
    {
        return $this->tryCatchWrapper(function () use ($siteId, $keyword) {
            return Item::where('site_id', $siteId)
                        ->where(function ($query) use ($keyword) {
                            $query->where('name', 'like', "%$keyword%")
                                  ->orWhere('item_code', 'like', "%$keyword%")
                                  ->orWhere('description', 'like', "%$keyword%")
                                  ->orWhereHas('category', function ($query) use ($keyword) {
                                      $query->where('name', 'like', "%$keyword%");
                                  });
                        })
                        ->get();
        }, '検索キーワードに基づく商品リストの取得に失敗しました');
    }

  }
