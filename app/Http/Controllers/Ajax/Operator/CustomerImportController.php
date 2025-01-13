<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ajax\Operator;

use App\Http\Controllers\Ajax\BaseAjaxController;
use App\Services\Customer\CustomerImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessCustomerImport;

class CustomerImportController extends BaseAjaxController
{
    private CustomerImportService $customerImportService;

    public function __construct(CustomerImportService $customerImportService)
    {
        $this->customerImportService = $customerImportService;
    }

    /**
     * 顧客データのインポート処理
     */
    public function import(Request $request): JsonResponse
    {
        try {
            // バリデーション
            $validator = validator($request->all(), [
                'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $auth = $this->getAuthenticatedUser();
            if (!$auth) {
                throw new \Exception('認証情報が見つかりません。');
            }

            // インポートタスクを作成
            $task = $this->customerImportService->createImportTask(
                $request->file('file')->getRealPath(),
                $auth->site_id,
                $auth->login_code
            );

            // 最初のジョブをキューに投入（開始行は0、バッチサイズは10）
            ProcessCustomerImport::dispatch($task, 0, 10);

            return response()->json([
                'success' => true,
                'data' => [
                    'taskCode' => $task->task_code
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'インポート処理に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * インポート処理の状態を確認
     */
    public function status(string $taskCode): JsonResponse
    {
        try {
            $task = $this->customerImportService->getImportTask($taskCode);
            if (!$task) {
                throw new \Exception('指定されたタスクが見つかりません。');
            }

            // レコード情報を取得
            $records = $task->importTaskRecords()
                ->orderBy('row_number')
                ->get()
                ->map(function ($record) {
                    $data = json_decode($record->data, true);
                    return [
                        'rowNumber' => $record->row_number,
                        'customerCode' => $data[0] ?? '',  // インデックスは実際のデータ構造に合わせて調整
                        'customerName' => $data[2] ?? '',  // インデックスは実際のデータ構造に合わせて調整
                        'statusLabel' => $this->getStatusLabel($record->status),
                        'statusClass' => $this->getStatusClass($record->status),
                        'processedAt' => $record->processed_at,
                        'errorMessage' => $record->error_message
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'task' => [
                        'status' => $task->status,
                        'statusMessage' => $task->status_message,
                        'totalRecords' => $task->total_records,
                        'processedRecords' => $task->processed_records,
                        'successRecords' => $task->success_records,
                        'errorRecords' => $task->error_records
                    ],
                    'records' => $records
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Import status check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'インポート状態の確認に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * レコードのステータスラベルを取得
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => '待機中',
            'processing' => '処理中',
            'completed' => '完了',
            'failed' => 'エラー',
            default => '不明'
        };
    }

    /**
     * レコードのステータスクラスを取得
     */
    private function getStatusClass(string $status): string
    {
        return match($status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * インポート処理の進捗確認画面を表示
     *
     * @param string $taskCode タスクコード
     * @return \Illuminate\View\View
     */
    public function progress(string $taskCode)
    {
        try {
            // オペレータ情報を取得
            $operator = $this->getAuthenticatedUser();
            if (!$operator) {
                abort(401, '認証情報が見つかりません。');
            }

            // インポートタスクを取得
            $task = $this->customerImportService->getImportTask($taskCode);
            if (!$task) {
                abort(404, '指定されたタスクが見つかりません。');
            }

            // サイトIDの確認
            if ($task->site_id !== $operator->site_id) {
                abort(403, 'このタスクにアクセスする権限がありません。');
            }

            return view('operator.customer.import.progress', [
                'operator' => $operator,
                'task' => $task
            ]);

        } catch (\Exception $e) {
            Log::error('Import progress page error: ' . $e->getMessage());
            abort(500, 'インポート進捗の表示に失敗しました。');
        }
    }
}
