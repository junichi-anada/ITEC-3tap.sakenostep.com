<?php

namespace App\Services\Operator\Customer\Import;

use App\Services\Operator\Customer\Import\GeneralImportService;
use App\Services\Operator\Customer\Import\Special\SakenoStep\SakenoStepImportService;
use Illuminate\Support\Facades\Log;

/**
 * インポートサービスクラス
 *
 * このクラスはデータのインポートを管理します。
 */
class ImportService
{
    private $generalImportService;
    private $sakenoStepImportService;

    public function __construct(
        GeneralImportService $generalImportService,
        SakenoStepImportService $sakenoStepImportService
    ) {
        $this->generalImportService = $generalImportService;
        $this->sakenoStepImportService = $sakenoStepImportService;
    }

    /**
     * データをインポートする
     *
     * @param string $filePath ファイルパス
     * @param string $siteCode サイトコード
     * @return array インポート結果
     */
    public function importData(string $filePath, string $siteCode): array
    {
        try {
            // サイトコードに基づいて仕様を選択
            if ($siteCode === 'sakeno_step') { // 例: SakenoStepのサイトコード
                return $this->sakenoStepImportService->import($filePath);
            } else {
                return $this->generalImportService->import($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}
