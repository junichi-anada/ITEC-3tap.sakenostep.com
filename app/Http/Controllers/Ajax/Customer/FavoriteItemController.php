<?php
/**
 * 顧客向けお気に入り商品管理機能 Api用コントローラー
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Ajax\BaseAjaxController;
use App\Services\FavoriteItem\FavoriteItemService;
use App\Services\Item\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class FavoriteItemController extends BaseAjaxController
{
    const SUCCESS_MESSAGE = 'お気に入りに追加しました';
    const DELETE_SUCCESS_MESSAGE = 'お気に入りから削除しました';
    const VALIDATION_ERROR_MESSAGE = 'バリデーションエラー';
    const UNEXPECTED_ERROR_MESSAGE = '予期しないエラーが発生しました';
    const INVALID_DETAIL_CODE_MESSAGE = '無効な注文詳細コードです';
    const NOT_FOUND_MESSAGE = 'お気に入りに登録されていません。';


    public function __construct(
        protected FavoriteItemService $favoriteItemService,
        protected ItemService $itemService,
    ) {
        $this->favoriteItemService = $favoriteItemService;
        $this->itemService = $itemService;
    }

    /**
     * お気に入りリストへの登録
     * お気に入りリストに商品を追加する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'item_code' => 'required|exists:items,item_code',
            ]);

            Log::info('お気に入りに追加する商品コード: ' . json_encode($request->input('item_code')));

            $auth = $this->getAuthenticatedUser();

            $item = $this->itemService->getByCodeOne(
                itemCode: $request->input('item_code'),
                siteId: $auth->site_id
            );
            Log::info('お気に入りに追加する商品ID: ' . json_encode($item->id));
            if (!$item->id) {
                throw new \Exception('商品IDが取得できませんでした');
            }

            $favoriteItem = $this->favoriteItemService->add($auth->id, $item->id, $auth->site_id);

            Log::info('お気に入り商品一覧: ' . json_encode($favoriteItem));

            return $this->jsonResponse(self::SUCCESS_MESSAGE, [
                'favorite_item' => $favoriteItem
            ]);

        } catch (Exception $exception) {
            Log::error('お気に入り商品の登録に失敗しました: ' . $exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);
            return $this->jsonResponse('お気に入り商品の登録に失敗しました', [], 500);
        }
    }

    /**
     * お気に入りの指定削除
     * パラメータのItemCodeに一致するお気に入り商品の登録を削除する。
     *
     * @param string $itemCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $item_code)
    {
        try {
            $auth = $this->getAuthenticatedUser();

            Log::info('お気に入りから消す商品コード: ' . json_encode($item_code));

            $item = $this->itemService->getByCodeOne(
                itemCode: $item_code,
                siteId: $auth->site_id
            );
            if (!$item->id) {
                throw new \Exception('商品IDが取得できませんでした');
            }
            Log::info('お気に入りから消す商品ID: ' . json_encode($item->id));

            $result = $this->favoriteItemService->remove($auth->id, $item->id, $auth->site_id);

            // 削除に失敗した場合は終了
            if (!$result) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            return $this->jsonResponse(self::DELETE_SUCCESS_MESSAGE, ['result' => $result], 201);
        } catch (\Exception $exception) {
            Log::error('お気に入り商品の削除に失敗しました: ' . $exception->getMessage());
            return response()->json(['message' => 'お気に入り商品の削除に失敗しました'], 500);
        }
    }


}
