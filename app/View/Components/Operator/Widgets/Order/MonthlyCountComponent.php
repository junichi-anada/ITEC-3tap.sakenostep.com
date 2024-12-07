<?php

namespace App\View\Components\Operator\Widgets\Order;

use App\Services\Order\Analytics\MonthlyOrderAnalytics;
use Carbon\Carbon;
use Illuminate\View\Component;

class MonthlyCountComponent extends Component
{
    public int $count;
    public int $month;

    public function __construct(MonthlyOrderAnalytics $analytics)
    {
        $this->count = $analytics->getMonthlyOrderCount();
        $this->month = Carbon::now()->month;
    }

    public function render()
    {
        return view('components.operator.widgets.order.monthly-count-component');
    }
}
