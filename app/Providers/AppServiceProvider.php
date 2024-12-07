<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Contracts\LineMessagingServiceInterface;
use App\Services\LineMessagingService;
use App\Services\Customer\Analytics\OperatorCustomerCountAnalytics;
use App\Services\Operator\Customer\Import\Special\SakenoStep\SakenoStepImportService;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use App\View\Components\Operator\Widgets\Customer\RegistEachAreaComponent;
use App\View\Components\Operator\Widgets\Order\MonthlyCountComponent;
use App\View\Components\Operator\Widgets\Order\TodayCountComponent;
use App\View\Components\Operator\Widgets\Item\PopularRankingComponent;
use App\View\Components\Operator\Widgets\Order\EachAreaOrderComponent;
use App\View\Components\Operator\Widgets\SystemInfo\SystemInfoComponent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // LINE Messaging Serviceのバインディング
        $this->app->bind(LineMessagingServiceInterface::class, LineMessagingService::class);

        // OperatorCustomerCountAnalyticsのバインディング
        $this->app->singleton(OperatorCustomerCountAnalytics::class, OperatorCustomerCountAnalytics::class);

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
        // Operator Widgets Components Registration
        Blade::component('operator.widgets.customer.regist-each-area-component', RegistEachAreaComponent::class);
        Blade::component('operator.widgets.order.monthly-count-component', MonthlyCountComponent::class);
        Blade::component('operator.widgets.order.today-count-component', TodayCountComponent::class);
        Blade::component('operator.widgets.item.popular-ranking-component', PopularRankingComponent::class);
        Blade::component('operator.widgets.order.each-area-order-component', EachAreaOrderComponent::class);
        Blade::component('operator.widgets.system-info.system-info-component', SystemInfoComponent::class);
    }
}
