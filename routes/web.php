<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\FavoriteItemController as UserFavoriteItemController;
use App\Http\Controllers\User\RecommendedItemController as UserRecommendedItemController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

/**
 * ウェルカムページ -> ここに飛んではいけない
 */
Route::get('welcome', function () { return view('welcome');})->name('welcome');


/**
 * ここから下は認証済みユーザーのみアクセス可能
 */
Route::middleware(['web', 'auth'])->group(function () {

    /**
     * 注文リスト
     */
    Route::prefix('order')->group(function () {
        Route::get('/', [UserOrderController::class, 'index'])->name('user.order');
        Route::post('/add', [UserOrderController::class, 'store'])->name('user.order.add');
        Route::delete('/remove/{detailCode}', [UserOrderController::class, 'destroy'])->name('user.order.remove');
    });

    /**
     * お気に入り商品関連のルーティング
     */
    Route::prefix('favorites')->group(function () {
        Route::post('/add', [UserFavoriteItemController::class, 'store'])->name('user.favorites.add');
        Route::delete('/remove/{id}', [UserFavoriteItemController::class, 'destroy'])->name('user.favorites.remove');
        Route::get('/', [UserFavoriteItemController::class, 'index'])->name('user.favorites.list');
    });


    /**
     * おすすめ商品関連のルーティング
     */
    Route::prefix('recommendations')->group(function () {
        Route::get('/', [UserRecommendedItemController::class, 'index'])->name('user.recommendations.index');
    });

});


