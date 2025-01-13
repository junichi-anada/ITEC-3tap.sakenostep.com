<?php

use App\Http\Controllers\Ajax\Operator\CustomerImportController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('operator/customer/import')->group(function () {
        Route::post('/', [CustomerImportController::class, 'import'])
            ->name('api.operator.customer.import');
        
        Route::get('{taskCode}/status', [CustomerImportController::class, 'status'])
            ->name('api.operator.customer.import.status');
    });
});
