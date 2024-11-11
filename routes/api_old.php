<?php
/**
 * API用のルート定義
 */

use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Api\Customer\CategoryController;
// use App\Http\Controllers\Api\Auth\LoginController as ApiLoginController;
// use App\Http\Controllers\Api\Customer\OrderController as ApiOrderController;
// use App\Http\Controllers\Api\Customer\ApiFavoriteItemController as ApiFavoriteItemController;
// use App\Http\Controllers\Api\Customer\RecommendedItemController;


// use App\Http\Controllers\Auth\LoginController;
// use App\Http\Controllers\User\OrderController;
// use App\Http\Controllers\User\FavoriteItemController;
// use App\Http\Controllers\Operator\CustomerController;

// 認証関連のルート
// Route::post('/login', [ApiLoginController::class, 'login']);
// Route::post('/logout', [ApiLoginController::class, 'logout']);

// ユーザー関連のルート
// Route::middleware('auth:sanctum')->group(function () {

    /**
     * 注文リスト
     */
    // Route::prefix('order')->group(function () {
    //     Route::post('/add', [ApiOrderController::class, 'store'])->name('user.order.item.list.add');
    //     Route::delete('/remove/{detailCode}', [ApiOrderController::class, 'destroy'])->name('user.order.item.list.remove');
    //     Route::delete('/removeAll', [ApiOrderController::class, 'destroyAll'])->name('user.order.item.list.remove.all');
    //     Route::post('/send', [ApiOrderController::class, 'order'])->name('user.order.item.list.order');
    //     Route::post('/addAll', [ApiOrderController::class, 'addAll'])->name('user.order.item.list.add.all');
    // });

    /**
     * お気に入り商品関連のルーティング
     */
//     Route::prefix('favorites')->group(function () {
//         Route::post('/add', [ApiFavoriteItemController::class, 'store'])->name('user.favorite.item.add');
//         Route::delete('/remove/{id}', [ApiFavoriteItemController::class, 'destroy'])->name('user.favorite.item.remove');
//     });

// });
