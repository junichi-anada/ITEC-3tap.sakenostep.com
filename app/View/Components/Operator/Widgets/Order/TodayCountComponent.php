<?php

namespace App\View\Components\Operator\Widgets\Order;

use App\Services\Order\Analytics\TodayOrderAnalytics;
use Carbon\Carbon;
use Illuminate\View\Component;

class TodayCountComponent extends Component
{
    public int $day;
    public int $count;
    public int $pendingExport;
    public int $completedExport;

    public function __construct(TodayOrderAnalytics $analytics)
    {
        $counts = $analytics->getTodayOrderCounts();
        $this->day = Carbon::now()->day;
        $this->count = $counts['total'];
        $this->pendingExport = $counts['not_exported'];
        $this->completedExport = $counts['exported'];
    }

    public function render()
    {
        return view('components.operator.widgets.order.today-count-component');
    }
}
