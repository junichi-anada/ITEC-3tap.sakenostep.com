<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\FavoriteItemController as UserFavoriteItemController;
use App\Http\Controllers\User\RecommendedItemController as UserRecommendedItemController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\SearchController as UserSearchController;
use App\Http\Controllers\User\CategoryController as UserCategoryController;
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
        Route::get('/', [UserOrderController::class, 'index'])->name('user.order.item.list');
        Route::post('/add', [UserOrderController::class, 'store'])->name('user.order.item.list.add');
        Route::delete('/remove/{detailCode}', [UserOrderController::class, 'destroy'])->name('user.order.item.list.remove');
    });

    /**
     * お気に入り商品関連のルーティング
     */
    Route::prefix('favorites')->group(function () {
        Route::post('/add', [UserFavoriteItemController::class, 'store'])->name('user.favorite.item.add');
        Route::delete('/remove/{id}', [UserFavoriteItemController::class, 'destroy'])->name('user.favorite.item.remove');
        Route::get('/', [UserFavoriteItemController::class, 'index'])->name('user.favorite.item.list');
    });

    /**
     * おすすめ商品関連のルーティング
     */
    Route::prefix('recommendations')->group(function () {
        Route::get('/', [UserRecommendedItemController::class, 'index'])->name('user.recommended.item.list');
    });

    /**
     * 商品一覧関連のルーティング
     */
    Route::prefix('categories')->group(function () {
        Route::get('/', [UserCategoryController::class, 'index'])->name('user.category.list');
        Route::get('/{code}', [UserCategoryController::class, 'show'])->name('user.category.item.list');
    });

    /**
     * 検索関連のルーティング
     */
    Route::prefix('search')->group(function () {
        Route::post('/', [UserSearchController::class, 'index'])->name('user.search.item.list');
        Route::get('/', [UserSearchController::class, 'index']);
    });

});

/* 管理者用のルーティング */

/**
 * ここから下は認証済みユーザーのみアクセス可能
 */
Route::middleware(['web', 'auth'])->group(function () {

    /**
     * ダッシュボード
     */
    Route::prefix('operator')->group(function () {
        Route::get('/dashboard', function () { return view('operator.dashboard');})->name('operator.dashboard');
    });

});
