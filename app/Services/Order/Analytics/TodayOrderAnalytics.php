<?php

namespace App\Services\Order\Analytics;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TodayOrderAnalytics
{
    public function getTodayOrderCounts(): array
    {
        $today = Carbon::today();

        $orders = Order::query()
            ->whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('COUNT(CASE WHEN exported_at IS NULL THEN 1 END) as not_exported_count')
            ->selectRaw('COUNT(CASE WHEN exported_at IS NOT NULL THEN 1 END) as exported_count')
            ->first();

        return [
            'total' => $orders->total_count,
            'not_exported' => $orders->not_exported_count,
            'exported' => $orders->exported_count,
        ];
    }
}
