<?php

namespace App\Services\OrderDetail\Traits;

use Illuminate\Support\Facades\Log;

trait OrderDetailServiceTrait
{
    /**
     * 例外処理を共通化するためのラッパーメソッド
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    protected function executeWithErrorHandling(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("Error: $errorMessage - " . $e->getMessage());
            throw new \Exception($errorMessage . ": " . $e->getMessage());
        }
    }
}
