<?

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthenticateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // サイトごとの認証エンドポイント
    Route::prefix('{site_id}')->group(function () {
        // オペレータ認証
        Route::post('/login/operator', [AuthenticateController::class, 'login'])
            ->name('auth.operator.login')
            ->whereNumber('site_id');

        // 顧客認証
        Route::post('/login/customer', [AuthenticateController::class, 'customerLogin'])
            ->name('auth.customer.login')
            ->whereNumber('site_id');

        // 認証済みユーザーのエンドポイント
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthenticateController::class, 'logout'])
                ->name('auth.logout');
        });
    });
});
