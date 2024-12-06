<?php

namespace App\Services\Interfaces;

interface OperatorServiceInterface
{
    /**
     * 操作者の権限を確認します
     *
     * @param int $operatorId
     * @return bool
     */
    public function hasPermission(int $operatorId): bool;

    /**
     * 操作ログを記録します
     *
     * @param int $operatorId
     * @param string $action
     * @param array $context
     * @return void
     */
    public function logOperation(int $operatorId, string $action, array $context = []): void;
}
