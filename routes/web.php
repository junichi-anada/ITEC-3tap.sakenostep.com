<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Operator\CustomerController as OperatorCustomerController;
use App\Http\Controllers\User\FavoriteItemController as UserFavoriteItemController;
use App\Http\Controllers\User\RecommendedItemController as UserRecommendedItemController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\SearchController as UserSearchController;
use App\Http\Controllers\User\CategoryController as UserCategoryController;
use App\Http\Controllers\User\PageController as UserPageController;
use App\Http\Controllers\User\HistoryController as UserHistoryController;
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
        Route::delete('/removeAll', [UserOrderController::class, 'destroyAll'])->name('user.order.item.list.remove.all');
        Route::post('/send', [UserOrderController::class, 'order'])->name('user.order.item.list.order');
        Route::post('/addAll', [UserOrderController::class, 'addAll'])->name('user.order.item.list.add.all');
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

    /**
     * 注文履歴のルーティング
     */
    Route::prefix('history')->group(function () {
        Route::get('/', [UserHistoryController::class, 'index'])->name('user.history.list');
        Route::get('/{order_code}', [UserHistoryController::class, 'detail'])->name('user.history.detail');
    });

    /**
     * 固定ページのルーティング
     */
    Route::prefix('about')->group(function () {
        Route::get('/order', [UserPageController::class, 'order'])->name('user.about.order');
        Route::get('/delivery', [UserPageController::class, 'delivery'])->name('user.about.delivery');
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
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('operator.dashboard');

        /**
         * 顧客管理
         */
        Route::prefix('customer')->group(function () {
            Route::get('/', [OperatorCustomerController::class, 'index'])->name('operator.customer.index'); // 顧客一覧
            Route::get('/create', [OperatorCustomerController::class, 'create'])->name('operator.customer.create'); // 顧客登録フォーム
            Route::post('/', [OperatorCustomerController::class, 'store'])->name('operator.customer.store'); // 顧客登録
            Route::get('/{id}', [OperatorCustomerController::class, 'show'])->name('operator.customer.show'); // 顧客詳細
            Route::put('/{id}', [OperatorCustomerController::class, 'update'])->name('operator.customer.update'); // 顧客更新
            Route::delete('/{id}', [OperatorCustomerController::class, 'destroy'])->name('operator.customer.destroy'); // 顧客削除
            Route::post('/search', [OperatorCustomerController::class, 'search'])->name('operator.customer.search'); // 顧客検索
            Route::post('/upload', [OperatorCustomerController::class, 'upload'])->name('operator.customer.upload'); // 顧客データアップロード
            Route::get('/upload/status', [OperatorCustomerController::class, 'status'])->name('operator.customer.status'); // アップロードステータス
        });

    });


    /**
     * エラーページ
     */
    Route::get('/error', function () { return view('operator.customer.error'); })->name('operator.customer.error');

});
