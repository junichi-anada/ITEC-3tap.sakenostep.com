<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Order\Analytics\OrderAnalyticsService;

class DashboardController extends Controller
{
    private OrderAnalyticsService $orderAnalyticsService;

    public function __construct(OrderAnalyticsService $orderAnalyticsService)
    {
        $this->orderAnalyticsService = $orderAnalyticsService;
    }

    public function index()
    {
        $orderStats = [
            'monthlyCount' => $this->orderAnalyticsService->getMonthlyOrderCount(),
            'todayCount' => $this->orderAnalyticsService->getTodayOrderCount(),
            'todayNotExportedCount' => $this->orderAnalyticsService->getTodayNotExportedCount(),
            'todayExportedCount' => $this->orderAnalyticsService->getTodayExportedCount(),
        ];

        return view('operator.dashboard', compact('orderStats'));
    }
}
