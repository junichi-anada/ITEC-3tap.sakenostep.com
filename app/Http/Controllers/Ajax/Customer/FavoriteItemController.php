<?php
/**
 * Apiコントローラ
 * 顧客向けお気に入り商品管理機能
 */
namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Controller;
use App\Services\FavoriteItem\Customer\ReadService as FavoriteItemReadService;
use App\Services\FavoriteItem\Customer\CreateService as FavoriteItemCreateService;
use App\Services\FavoriteItem\Customer\DeleteService as FavoriteItemDeleteService;
use App\Services\Item\Customer\ReadService as ItemReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FavoriteItemController extends Controller
{
    protected $itemReadService;
    protected $favoriteItemReadService;
    protected $favoriteItemCreateService;
    protected $favoriteItemDeleteService;

    const SUCCESS_MESSAGE = 'お気に入りに追加しました';
    const ALREADY_EXISTS_MESSAGE = 'すでにお気に入りに追加されています';
    const NOT_FOUND_MESSAGE = '対象の商品が見つかりません';
    const DELETE_SUCCESS_MESSAGE = 'お気に入りから削除しました';
    const DELETE_FAILURE_MESSAGE = 'お気に入りから削除に失敗しました';
    const VALIDATION_ERROR_MESSAGE = 'バリデーションエラー';
    const UNEXPECTED_ERROR_MESSAGE = '予期しないエラーが発生しました';

    public function __construct(
        ItemReadService $itemReadService,
        FavoriteItemReadService $favoriteItemReadService,
        FavoriteItemCreateService $favoriteItemCreateService,
        FavoriteItemDeleteService $favoriteItemDeleteService
    ) {
        $this->itemReadService = $itemReadService;
        $this->favoriteItemReadService = $favoriteItemReadService;
        $this->favoriteItemCreateService = $favoriteItemCreateService;
        $this->favoriteItemDeleteService = $favoriteItemDeleteService;
    }

    private function getAuthenticatedUser()
    {
        return Auth::user();
    }

    private function getItemByCode(string $item_code)
    {
        return $this->itemReadService->getByItemCode($item_code);
    }

    private function jsonResponse($message, $data = [], $status = 200)
    {
        return response()->json(array_merge(['message' => $message], $data), $status);
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'item_code' => 'required|exists:items,item_code',
        ]);
    }

    private function getFavoriteItem($auth, $item)
    {
        return $this->favoriteItemReadService->getByUserIdAndItemIdWithTrashed($auth->id, $item->id, $auth->site_id);
    }

    private function createFavoriteItem($auth, $item)
    {
        return $this->favoriteItemCreateService->create($auth->id, $item->id, $auth->site_id);
    }

    /**
     * お気に入り商品への登録
     */
    public function store(Request $request)
    {
        try {
            $this->validateRequest($request);

            $auth = $this->getAuthenticatedUser();
            $item = $this->getItemByCode($request->item_code);

            if (!$item) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $favoriteItem = $this->getFavoriteItem($auth, $item);
            if ($favoriteItem) {
                return $this->handleExistingFavoriteItem($favoriteItem);
            }

            $favoriteItem = $this->createFavoriteItem($auth, $item);
            if (!$favoriteItem) {
                return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, [], 500);
            }

            return $this->jsonResponse(self::SUCCESS_MESSAGE, ['favoriteItem' => $favoriteItem], 201);
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return $this->jsonResponse(self::VALIDATION_ERROR_MESSAGE, ['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, [], 500);
        }
    }

    private function handleExistingFavoriteItem($favoriteItem)
    {
        if ($favoriteItem->trashed()) {
            $favoriteItem->restore();
            return $this->jsonResponse(self::SUCCESS_MESSAGE, ['favoriteItem' => $favoriteItem], 201);
        } else {
            return $this->jsonResponse(self::ALREADY_EXISTS_MESSAGE, [], 409);
        }
    }

    /**
     * お気に入り商品からの削除
     */
    public function destroy($item_code)
    {
        try {
            $auth = Auth::user();
            Log::info("Deleting item with code: $item_code for user: {$auth->id} on site: {$auth->site_id}");

            $item = $this->itemReadService->getByItemCode($item_code);
            if (!$item) {
                Log::error("Item with code $item_code not found.");
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $favoriteItem = $this->favoriteItemReadService->getByUserIdAndItemId($auth->id, $item->id, $auth->site_id);
            if (!$favoriteItem) {
                return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
            }

            $process = $this->favoriteItemDeleteService->softDelete($auth->id, $item_code, $auth->site_id);
            if (!$process) {
                return $this->jsonResponse(self::DELETE_FAILURE_MESSAGE, [], 500);
            }
            return $this->jsonResponse(self::DELETE_SUCCESS_MESSAGE);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, [], 500);
        }
    }
}
