<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Contracts\LineMessagingServiceInterface;
use App\Services\Messaging\LineMessagingService;
use App\Services\Customer\Analytics\OperatorCustomerCountAnalytics;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use App\View\Components\Operator\Widgets\Customer\CustomerListComponent;
use App\View\Components\Operator\Widgets\Customer\CustomerSearchFormComponent;
use App\View\Components\Operator\Widgets\Customer\CustomerRegistrationComponent;
use App\View\Components\Operator\Widgets\Customer\LineMessageFormComponent;
use App\View\Components\Operator\Widgets\Order\MonthlyCountComponent;
use App\View\Components\Operator\Widgets\Order\TodayCountComponent;
use App\View\Components\Operator\Widgets\Item\PopularRankingComponent;
use App\View\Components\Operator\Widgets\Order\EachAreaOrderComponent;
use App\View\Components\Operator\Widgets\SystemInfo\SystemInfoComponent;
use GuzzleHttp\Client;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // LINE Messaging APIのバインディング
        $this->app->singleton(MessagingApiApi::class, function ($app) {
            $client = new Client();
            $config = new Configuration();
            $config->setAccessToken(config('services.line.channel_token'));
            return new MessagingApiApi(
                client: $client,
                config: $config
            );
        });

        // LINE Messaging Serviceのバインディング
        $this->app->bind(LineMessagingServiceInterface::class, LineMessagingService::class);

        // OperatorCustomerCountAnalyticsのバインディング
        $this->app->singleton(OperatorCustomerCountAnalytics::class, OperatorCustomerCountAnalytics::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Operator Widgets Components Registration
        Blade::component('operator.widgets.customer.customer-list-component', CustomerListComponent::class);
        Blade::component('operator.widgets.customer.customer-search-form-component', CustomerSearchFormComponent::class);
        Blade::component('operator.widgets.customer.customer-registration-component', CustomerRegistrationComponent::class);
        Blade::component('operator.widgets.customer.line-message-form-component', LineMessageFormComponent::class);
        Blade::component('operator.widgets.order.monthly-count-component', MonthlyCountComponent::class);
        Blade::component('operator.widgets.order.today-count-component', TodayCountComponent::class);
        Blade::component('operator.widgets.item.popular-ranking-component', PopularRankingComponent::class);
        Blade::component('operator.widgets.order.each-area-order-component', EachAreaOrderComponent::class);
        Blade::component('operator.widgets.system-info.system-info-component', SystemInfoComponent::class);
    }
}
