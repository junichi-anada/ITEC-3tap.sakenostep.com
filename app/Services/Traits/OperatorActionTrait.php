<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Log;
use App\Models\Operator;
use App\Models\SiteOperator;

trait OperatorActionTrait
{
    /**
     * 操作者の権限を確認します
     *
     * @param int $operatorId
     * @return bool
     */
    public function hasPermission(int $operatorId): bool
    {
        $operator = SiteOperator::where('operator_id', $operatorId)
            ->where('is_active', true)
            ->first();

        return $operator !== null;
    }

    /**
     * 操作ログを記録します
     *
     * @param int $operatorId
     * @param string $action
     * @param array $context
     * @return void
     */
    public function logOperation(int $operatorId, string $action, array $context = []): void
    {
        $operator = Operator::find($operatorId);
        $logContext = array_merge([
            'operator_id' => $operatorId,
            'operator_name' => $operator?->name ?? 'Unknown',
            'action' => $action,
        ], $context);

        Log::info('Operator Action: ' . $action, $logContext);
    }

    /**
     * トランザクションの実行を記録します
     *
     * @param int $operatorId
     * @param string $transactionType
     * @param array $context
     * @return void
     */
    protected function logTransaction(int $operatorId, string $transactionType, array $context = []): void
    {
        $this->logOperation($operatorId, "Transaction: {$transactionType}", $context);
    }

    /**
     * バリデーションエラーを記録します
     *
     * @param int $operatorId
     * @param string $validationType
     * @param array $errors
     * @return void
     */
    protected function logValidationError(int $operatorId, string $validationType, array $errors): void
    {
        $this->logOperation($operatorId, "Validation Error: {$validationType}", ['errors' => $errors]);
    }
}
