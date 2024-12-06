<?php

namespace App\Services\Operator\Customer\Import\Special\SakenoStep;

use App\Models\ImportTask;
use App\Services\Operator\Customer\Import\GeneralImportService;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    // Start of Selection
    /**
     * タスク作成
     *
     * @param string $filePath ファイルパス
     * @return App\Models\ImportTask|null タスクコードまたはnull
     */
    public function createTask(string $filePath): ?ImportTask
    {
        try {
            $task = ImportTask::create([
                'task_code' => (string) Str::uuid(),
                'site_id' => 1,
                'data_type' => 'customer',
                'file_path' => $filePath,
                'status' => 'pending',
                'status_message' => null,
                'imported_by' => auth()->user()->id,
                'uploaded_at' => now(),
                'imported_at' => null,
            ]);
            return $task;
        } catch (\Exception $e) {
            $this->logService->logError('タスク作成に失敗しました: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * データをデータベースにインポートします。
     *
     * @param array $formattedData インポートする整形済みデータ
     * @return void
     * @throws \Exception データベースへのインポートに失敗した場合
     */
    public function importToDatabase(array $formattedData)
    {
        $auth = Auth::user();
        $chunkSize = 1000; // 一度に処理するデータ数

        try {
            // データを1000件ずつに分割して処理
            foreach (array_chunk($formattedData, $chunkSize) as $chunk) {
                \DB::beginTransaction();

                // Start of Selection
                foreach ($formattedData as $data) {
                    try {
                        /**
                         * ユーザー情報を抽出します
                         *
                         * @var array $userData ユーザー情報の配列
                         */
                        $userData = [
                            'user_code' => $data['code'],
                            'site_id' => $auth->site_id,
                            'name' => $data['name'],
                            'postal_code' => $data['postal_code'],
                            'address' => $data['address'],
                            'phone' => $data['phone'],
                            'phone2' => $data['phone2'],
                            'fax' => $data['fax'],
                        ];

                        /**
                         * 認証情報を抽出します
                         *
                         * @var array $authenticateData 認証情報の配列
                         */
                        $authenticateData = [
                            'auth_code' => Str::uuid(),
                            'site_id' => $auth->site_id,
                            'entity_type' => 'App\Models\User',
                            'login_code' => $data['code'],
                            'password' => Hash::make($data['phone']),
                            'expires_at' => now()->addDays(365),
                        ];

                        // ユーザーデータをデータベースのusersテーブルに挿入します
                        $userId = \DB::table('users')->insertGetId($userData);
                        $authenticateData['entity_id'] = $userId;

                        // 認証データをデータベースのauthenticatesテーブルに挿入します
                        \DB::table('authenticates')->insert($authenticateData);
                    } catch (\Exception $e) {
                        \DB::rollBack();
                        \Log::error('importToDatabaseメソッドでエラーが発生しました。データ: ' . json_encode($data) . ' エラーメッセージ: ' . $e->getMessage());
                        throw $e;
                    }
                }
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('importToDatabaseメソッドでエラーが発生しました。引数: ' . json_encode($formattedData) . ' エラーメッセージ: ' . $e->getMessage());
            throw $e;
        }
    }

}
