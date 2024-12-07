<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Web\Operator\CustomerController as OperatorCustomerController;
use App\Http\Controllers\Web\Operator\OrderController as OperatorOrderController;
use App\Http\Controllers\Web\Operator\ItemController as OperatorItemController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Customer\OrderController as CustomerOrderWebController;
use App\Http\Controllers\Ajax\Customer\OrderController as CustomerOrderAjaxController;
use App\Http\Controllers\Web\Customer\RecommendedItemController as CustomerRecommendedItemWebController;
use App\Http\Controllers\Ajax\Customer\FavoriteItemController as CustomerFavoriteItemAjaxController;
use App\Http\Controllers\Web\Customer\FavoriteItemController as CustomerFavoriteItemWebController;
use App\Http\Controllers\Web\Customer\PageController as CustomerPageWebController;
use App\Http\Controllers\Web\Customer\CategoryController as CustomerCategoryWebController;
use App\Http\Controllers\Web\Customer\SearchController as CustomerSearchWebController;
use App\Http\Controllers\Web\Customer\HistoryController;
use App\Http\Controllers\Ajax\Customer\HistoryController as HistoryAjaxController;
use App\Http\Controllers\LineAuthController;
use App\Http\Controllers\LineWebhookController;
use App\Http\Controllers\LineMessageController;
use App\Http\Controllers\LineAccountLinkController;

Route::get('/login', [LoginController::class, 'index'])->name('login');
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
        Route::get('/', [CustomerOrderWebController::class, 'index'])->name('user.order.item.list');
        Route::post('/add', [CustomerOrderAjaxController::class, 'store'])->name('user.order.item.list.add');
        Route::delete('/remove/{item_code}', [CustomerOrderAjaxController::class, 'destroy'])->name('user.order.item.list.remove');
        Route::delete('/removeAll', [CustomerOrderAjaxController::class, 'destroyAll'])->name('user.order.item.list.remove.all');
        Route::post('/send', [CustomerOrderAjaxController::class, 'order'])->name('user.order.item.list.order');
    });

    /**
     * お気に入り商品関連のルーティング
     */
    Route::prefix('favorites')->group(function () {
        Route::post('/add', [CustomerFavoriteItemAjaxController::class, 'store'])->name('user.favorite.item.add');
        Route::delete('/remove/{item_code}', [CustomerFavoriteItemAjaxController::class, 'destroy'])->name('user.favorite.item.remove');
        Route::get('/', [CustomerFavoriteItemWebController::class, 'index'])->name('user.favorite.item.list');
    });

    /**
     * おすすめ商品関連のルーティング
     */
    Route::prefix('recommendations')->group(function () {
        Route::get('/', [CustomerRecommendedItemWebController::class, 'index'])->name('user.recommended.item.list');
    });

    /**
     * 商品一覧関連のルーティング
     */
    Route::prefix('category')->group(function () {
        Route::get('/', [CustomerCategoryWebController::class, 'index'])->name('user.category.list');
        Route::get('/{code}', [CustomerCategoryWebController::class, 'show'])->name('user.category.item.list');
    });

    /**
     * 検索関連のルーティング
     */
    Route::prefix('search')->group(function () {
        Route::post('/', [CustomerSearchWebController::class, 'index'])->name('user.search.item.list');
        Route::get('/', [CustomerSearchWebController::class, 'index']);
    });

    /**
     * 注文履歴のルーティング
     */
    Route::prefix('history')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])->name('user.history.list');
        Route::get('/{order_code}', [HistoryController::class, 'show'])->name('user.history.detail');
        Route::post('/addAll', [HistoryAjaxController::class, 'addAll'])->name('user.order.item.list.add.all');
    });

    /**
     * 固定ページのルーティング
     */
    Route::prefix('about')->group(function () {
        Route::get('/order', [CustomerPageWebController::class, 'order'])->name('user.about.order');
        Route::get('/delivery', [CustomerPageWebController::class, 'delivery'])->name('user.about.delivery');
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
            Route::get('/', [OperatorCustomerController::class, 'index'])->name('operator.customer.index');
            Route::get('/create', [OperatorCustomerController::class, 'create'])->name('operator.customer.create');
            Route::post('/', [OperatorCustomerController::class, 'store'])->name('operator.customer.store');
            Route::get('/{id}', [OperatorCustomerController::class, 'show'])->name('operator.customer.show');
            Route::put('/{id}', [OperatorCustomerController::class, 'update'])->name('operator.customer.update');
            Route::delete('/{id}', [OperatorCustomerController::class, 'destroy'])->name('operator.customer.destroy');
            Route::post('/search', [OperatorCustomerController::class, 'search'])->name('operator.customer.search');
            Route::post('/upload', [OperatorCustomerController::class, 'upload'])->name('operator.customer.upload');
            Route::get('/upload/status', [OperatorCustomerController::class, 'status'])->name('operator.customer.status');
        });

        /**
         * 注文管理
         */
        Route::prefix('order')->group(function () {
            Route::get('/', [OperatorOrderController::class, 'index'])->name('operator.order.index');
            Route::get('/create', [OperatorOrderController::class, 'create'])->name('operator.order.create');
            Route::post('/', [OperatorOrderController::class, 'store'])->name('operator.order.store');
            Route::get('/{id}', [OperatorOrderController::class, 'show'])->name('operator.order.show');
            Route::put('/{id}', [OperatorOrderController::class, 'update'])->name('operator.order.update');
            Route::delete('/{id}', [OperatorOrderController::class, 'destroy'])->name('operator.order.destroy');
            Route::post('/search', [OperatorOrderController::class, 'search'])->name('operator.order.search');
            Route::post('/upload', [OperatorOrderController::class, 'upload'])->name('operator.order.upload');
            Route::get('/upload/status', [OperatorOrderController::class, 'status'])->name('operator.order.status');
        });

        /**
         * 商品管理
         */
        Route::prefix('item')->group(function () {
            Route::get('/', [OperatorItemController::class, 'index'])->name('operator.item.index');
            Route::get('/create', [OperatorItemController::class, 'create'])->name('operator.item.create');
            Route::post('/', [OperatorItemController::class, 'store'])->name('operator.item.store');
            Route::get('/{id}', [OperatorItemController::class, 'show'])->name('operator.item.show');
            Route::put('/{id}', [OperatorItemController::class, 'update'])->name('operator.item.update');
            Route::delete('/{id}', [OperatorItemController::class, 'destroy'])->name('operator.item.destroy');
            Route::post('/search', [OperatorItemController::class, 'search'])->name('operator.item.search');
            Route::post('/upload', [OperatorItemController::class, 'upload'])->name('operator.item.upload');
            Route::get('/upload/status', [OperatorItemController::class, 'status'])->name('operator.item.status');
        });
    });

    /**
     * エラーページ
     */
    Route::get('/error', function () { return view('operator.customer.error'); })->name('operator.customer.error');
});

Route::get('/customer/history/detail', [HistoryController::class, 'detail']);

/**
 * LINEログイン
 */
Route::get('/line/login', [LineAuthController::class, 'redirectToLine'])->name('line.login');
Route::get('/line/callback', [LineAuthController::class, 'handleLineCallback'])->name('line.callback');

/**
 * LINE Webhook
 */
Route::post('/line/webhook', [LineWebhookController::class, 'handle']);
Route::get('/line/send-message', [LineMessageController::class, 'send']);

/**
 * LINEアカウント連携
 */
Route::prefix('line/account')->group(function () {
    Route::get('/callback', [LineAccountLinkController::class, 'callback'])->name('line.account.callback');
    Route::post('/unlink', [LineAccountLinkController::class, 'unlink'])
        ->middleware(['auth'])
        ->name('line.account.unlink');
    Route::view('/success', 'line.success')->name('line.success');
    Route::view('/error', 'line.error')->name('line.error');
});
