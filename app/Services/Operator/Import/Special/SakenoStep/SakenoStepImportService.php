<?php

namespace App\Services\Operator\Import\Special\SakenoStep;

use App\Services\Operator\Import\GeneralImportService;
use Illuminate\Support\Facades\Log;

/**
 * SakenoStepインポートサービスクラス
 *
 * このクラスはSakenoStep仕様のデータインポートを処理します。
 */
class SakenoStepImportService extends GeneralImportService
{
    /**
     * データをインポートする
     *
     * @param string $filePath ファイルパス
     * @return array インポート結果
     */
    public function import(string $filePath): array
    {
        try {
            // 共通の処理を実行
            $result = parent::import($filePath);

            // 独自のデータ整形やバリデーションを追加
            $this->customProcess($filePath);

            return $result;
        } catch (\Exception $e) {
            Log::error('SakenoStep import failed: ' . $e->getMessage());
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
