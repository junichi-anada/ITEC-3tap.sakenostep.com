<?php

namespace App\Services\Order\Analytics;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyOrderAnalytics
{
    public function getMonthlyOrderCount(): int
    {
        return Order::query()
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
    }
}
