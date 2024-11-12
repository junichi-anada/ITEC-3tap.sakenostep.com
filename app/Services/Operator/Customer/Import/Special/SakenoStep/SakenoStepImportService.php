<?php

namespace App\Services\Operator\Customer\Import\Special\SakenoStep;

use App\Services\Operator\Customer\Import\GeneralImportService;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * SakenoStepインポートサービスクラス
 *
 * このクラスはSakenoStep仕様のデータインポートを処理します。
 */
class SakenoStepImportService extends GeneralImportService
{
    private CustomerLogService $logService;
    private CustomerTransactionService $transactionService;

    public function __construct(
        CustomerLogService $logService,
        CustomerTransactionService $transactionService
    ) {
        parent::__construct($logService, $transactionService);
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
                // 共通の処理を実行
                $result = parent::import($filePath);

                // 独自のデータ整形やバリデーションを追加
                $this->customProcess($filePath);

                return $result;
            });
        } catch (\Exception $e) {
            $this->logService->logError('SakenoStep import failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }

    /**
     * 独自のデータ処理を行う
     *
     * @param string $filePath ファイルパス
     * @return void
     */
    private function customProcess(string $filePath): void
    {
        // SakenoStep特有のデータ処理ロジック
    }
}
