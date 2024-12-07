<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Customer;

use App\Services\Customer\Analytics\OperatorCustomerCountAnalytics;
use App\Services\Customer\DTOs\CustomerRegistrationData;
use Illuminate\View\Component;

/**
 * ユーザー登録状況表示ウィジェット
 */
class CustomerRegistrationComponent extends Component
{
    /**
     * @var CustomerRegistrationData ユーザー登録状況データ
     */
    public readonly CustomerRegistrationData $data;

    public function __construct(
        private readonly OperatorCustomerCountAnalytics $analytics
    ) {
        $this->data = $this->analytics->getCustomerRegistrationSummary();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.customer-registration-component', [
            'userCount' => $this->data->totalUsers,
            'newUserCount' => $this->data->newUsersThisMonth,
            'lineUserCount' => $this->data->lineLinkedUsers,
        ]);
    }
}
