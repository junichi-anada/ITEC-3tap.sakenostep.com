<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Ajax\BaseAjaxController;
use App\Services\FavoriteItem\FavoriteItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * お気に入り商品管理コントローラー
 */
final class FavoriteItemController extends BaseAjaxController
{
    private const ALREADY_EXISTS_MESSAGE = 'already_exists';

    public function __construct(
        private readonly FavoriteItemService $favoriteItemService
    ) {}

    /**
     * お気に入り商品への登録
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'item_code' => 'required|string|max:255',
            ]);

            $auth = $this->getAuthUser();
            Log::info('Adding item to favorites', [
                'item_code' => $request->item_code,
                'user_id' => $auth->id,
                'site_id' => $auth->site_id
            ]);

            $result = $this->favoriteItemService->addToFavorites(
                $request->item_code,
                $auth->id,
                $auth->site_id
            );

            return $this->success($result, 201);

        } catch (ValidationException $e) {
            $this->logError($e, 'Favorite item validation error');
            return $this->error(self::VALIDATION_ERROR_MESSAGE, $e->errors(), 422);
        } catch (\Exception $e) {
            $this->logError($e, 'Unexpected error while adding favorite item');
            return $this->error(self::UNEXPECTED_ERROR_MESSAGE, [], 500);
        }
    }
}
