<?php

namespace App\Services\Operator\Customer\Import;

use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * 一般インポートサービスクラス
 *
 * このクラスは一般仕様のデータインポートを処理します。
 */
class GeneralImportService
{
    private CustomerLogService $logService;
    private CustomerTransactionService $transactionService;

    public function __construct(
        CustomerLogService $logService,
        CustomerTransactionService $transactionService
    ) {
        $this->logService = $logService;
        $this->transactionService = $transactionService;
    }

    /**
     * データをインポートする
     *
     * @param string $filePath ファイルパス
     * @return array インポート結果
     */
    public function import(string $filePath): array
    {
        try {
            return $this->transactionService->execute(function () use ($filePath) {
                // 共通のデータ読み込みと処理
                return $this->processData($filePath);
            });
        } catch (\Exception $e) {
            $this->logService->logError('General import failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }

    /**
     * データを処理する
     *
     * @param string $filePath ファイルパス
     * @return array 処理結果
     */
    protected function processData(string $filePath): array
    {
        // 共通のデータ処理ロジック
        return ['message' => 'success'];
    }
}
