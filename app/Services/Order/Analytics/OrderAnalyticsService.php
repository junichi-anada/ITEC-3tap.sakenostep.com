<?php

namespace App\Services\Order\Analytics;

use App\Models\Order;
use App\Services\ServiceErrorHandler;
use Illuminate\Support\Facades\Log;

/**
 * 注文の集計に関するサービスクラス
 * 
 * @package App\Services\Order\Analytics
 */
class OrderAnalyticsService
{
    use ServiceErrorHandler;

    /**
     * 今月の注文総数を取得
     *
     * @return int
     */
    public function getMonthlyOrderCount(): int
    {
        return $this->executeWithErrorHandling(
            fn () => Order::thisMonth()->count(),
            '今月の注文数の取得に失敗しました'
        );
    }

    /**
     * 本日の注文総数を取得
     *
     * @return int
     */
    public function getTodayOrderCount(): int
    {
        return $this->executeWithErrorHandling(
            fn () => Order::today()->count(),
            '本日の注文数の取得に失敗しました'
        );
    }

    /**
     * 本日のCSV未書出注文数を取得
     *
     * @return int
     */
    public function getTodayNotExportedCount(): int
    {
        return $this->executeWithErrorHandling(
            function () {
                // クエリビルダーを直接使用して、キャッシュの問題を回避
                return Order::whereDate('created_at', now()->toDateString())
                    ->whereNotNull('ordered_at')
                    ->whereNull('exported_at')
                    ->count();
            },
            '本日のCSV未書出注文数の取得に失敗しました'
        );
    }

    /**
     * 本日のCSV書出済注文数を取得
     *
     * @return int
     */
    public function getTodayExportedCount(): int
    {
        return $this->executeWithErrorHandling(
            function () {
                // クエリビルダーを直接使用して、キャッシュの問題を回避
                return Order::whereDate('created_at', now()->toDateString())
                    ->whereNotNull('ordered_at')
                    ->whereNotNull('exported_at')
                    ->count();
            },
            '本日のCSV書出済注文数の取得に失敗しました'
        );
    }

    /**
     * エラーハンドリング用のラッパーメソッド
     *
     * @param callable $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function executeWithErrorHandling(callable $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error($errorMessage, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->setError($errorMessage);
            return 0;
        }
    }
}
