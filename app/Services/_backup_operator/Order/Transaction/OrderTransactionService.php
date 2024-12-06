<?php

namespace App\Services\Operator\Order\Transaction;

use Illuminate\Support\Facades\DB;

/**
 * 注文トランザクションサービスクラス
 *
 * このクラスは注文関連のトランザクション管理を提供します。
 */
class OrderTransactionService
{
    /**
     * トランザクションを実行する
     *
     * @param callable $callback コールバック関数
     * @return mixed コールバック関数の戻り値
     * @throws \Exception トランザクション中にエラーが発生した場合
     */
    public function execute(callable $callback)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
