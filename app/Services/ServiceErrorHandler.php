<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

trait ServiceErrorHandler
{
    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    private function tryCatchWrapper($callback, string $errorMessage, array $context = [])
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error($errorMessage . ': ' . $e->getMessage(), $context);
            throw $e;
        }
    }
}
