<?php

namespace App\View\Components\Operator\Widgets\Order;

use Illuminate\View\Component;
use App\Services\Order\Analytics\OrderAnalyticsService;
use Carbon\Carbon;

class MonthlyCountComponent extends Component
{
    public int $month;
    public int $count;

    /**
     * コンポーネントを作成
     */
    public function __construct(OrderAnalyticsService $orderAnalyticsService)
    {
        $this->month = Carbon::now()->month;
        $this->count = $orderAnalyticsService->getMonthlyOrderCount();
    }

    /**
     * コンポーネントを描画
     */
    public function render()
    {
        return view('components.operator.widgets.order.monthly-count-component');
    }
}
