<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoriteItemController;

Route::get('/', function () {
    return view('index');
});

Route::get('order', function () {
    return view('user.order');
});

/**
 * お気に入り商品関連のルーティング
 */
Route::prefix('favorites')->group(function () {
    Route::post('/add', [FavoriteItemController::class, 'store'])->name('favorites.add');
    Route::delete('/remove/{id}', [FavoriteItemController::class, 'destroy'])->name('favorites.remove');
    Route::get('/', [FavoriteItemController::class, 'index'])->name('favorites.list');
});
