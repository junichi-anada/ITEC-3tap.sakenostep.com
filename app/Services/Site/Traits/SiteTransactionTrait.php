<?php

namespace App\Services\Site\Traits;

use App\Services\ServiceErrorHandler;
use Illuminate\Support\Facades\DB;

trait SiteTransactionTrait
{
    use ServiceErrorHandler;

    /**
     * トランザクションを使用した操作を実行する
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @param array $context
     * @return mixed
     */
    protected function executeTransaction(\Closure $callback, string $errorMessage, array $context = [])
    {
        return $this->tryCatchWrapper(
            fn () => DB::transaction($callback),
            $errorMessage,
            $context
        );
    }
}
