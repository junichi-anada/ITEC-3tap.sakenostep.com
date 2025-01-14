<?php

namespace App\View\Components\Operator\Widgets\Order;

use Illuminate\View\Component;
use App\Services\Order\Analytics\OrderAnalyticsService;
use Carbon\Carbon;

class TodayCountComponent extends Component
{
    public int $day;
    public int $count;
    public int $pendingExport;
    public int $completedExport;

    /**
     * コンポーネントを作成
     */
    public function __construct(OrderAnalyticsService $orderAnalyticsService)
    {
        $this->day = Carbon::now()->day;
        $this->count = $orderAnalyticsService->getTodayOrderCount();
        $this->pendingExport = $orderAnalyticsService->getTodayNotExportedCount();
        $this->completedExport = $orderAnalyticsService->getTodayExportedCount();
    }

    /**
     * コンポーネントを描画
     */
    public function render()
    {
        return view('components.operator.widgets.order.today-count-component');
    }
}
