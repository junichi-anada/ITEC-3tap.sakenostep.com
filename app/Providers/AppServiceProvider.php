<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\LineMessagingServiceInterface;
use App\Services\LineMessagingService;
use App\Services\Operator\Customer\Read\Component\Count\CountService;
use App\Services\Operator\Customer\Read\Component\Count\UserCountService;
use App\Services\Operator\Customer\Read\Component\Count\NewUserCountService;
use App\Services\Operator\Customer\Read\Component\Count\LineUserCountService;
use App\Services\Operator\Customer\Import\Special\SakenoStep\SakenoStepImportService;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // LINE Messaging Serviceのバインディング
        $this->app->bind(LineMessagingServiceInterface::class, LineMessagingService::class);

        // CountServiceとその依存関係のバインディング
        $this->app->singleton(CountService::class, function ($app) {
            return new CountService(
                $app->make(UserCountService::class),
                $app->make(NewUserCountService::class),
                $app->make(LineUserCountService::class)
            );
        });

        $this->app->singleton(UserCountService::class, UserCountService::class);
        $this->app->singleton(NewUserCountService::class, NewUserCountService::class);
        $this->app->singleton(LineUserCountService::class, LineUserCountService::class);

        // SakenoStepImportServiceのバインディング
        $this->app->singleton(SakenoStepImportService::class, function ($app) {
            return new SakenoStepImportService(
                $app->make(CustomerLogService::class),
                $app->make(CustomerTransactionService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
