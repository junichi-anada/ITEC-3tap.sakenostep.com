<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoriteItemController;
use App\Http\Controllers\RecommendedItemController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');



Route::middleware(['web', 'auth'])->group(function () {

    /**
     * 注文リスト
     */
    Route::get('order', function () { return view('user.order');})->name('order');


    /**
     * お気に入り商品関連のルーティング
     */
    Route::prefix('favorites')->group(function () {
        Route::post('/add', [FavoriteItemController::class, 'store'])->name('favorites.add');
        Route::delete('/remove/{id}', [FavoriteItemController::class, 'destroy'])->name('favorites.remove');
        Route::get('/', [FavoriteItemController::class, 'index'])->name('favorites.list');
    });


    /**
     * おすすめ商品関連のルーティング
     */
    Route::prefix('recommendations')->group(function () {
        Route::get('/', [RecommendedItemController::class, 'index'])->name('user.recommendations.index');
        Route::post('/add-to-favorites', [RecommendedItemController::class, 'addToFavorites'])->name('user.recommendations.addToFavorites');
        Route::post('/add-to-order-list', [RecommendedItemController::class, 'addToOrderList'])->name('user.recommendations.addToOrderList');
        Route::delete('/remove/{id}', [RecommendedItemController::class, 'destroy'])->name('user.recommendations.remove');
    });

});




/**
 * ウェルカムページ
 * ここに飛んではいけない
 */
Route::get('welcome', function () { return view('welcome');})->name('welcome');



// Route::get('order', function () {
//     return view('user.order');
// });


