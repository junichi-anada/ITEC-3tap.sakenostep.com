<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ajax\Operator;

use App\Http\Controllers\Ajax\BaseAjaxController;
use App\Services\Item\ItemImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessItemImport;
use App\Models\ImportTask;
use App\Models\ImportTaskRecord;
use Carbon\Carbon;

class ItemImportController extends BaseAjaxController
{
    private ItemImportService $itemImportService;

    public function __construct(ItemImportService $itemImportService)
    {
        $this->itemImportService = $itemImportService;
    }

    /**
     * 商品データのインポート処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        try {
            Log::info('商品データインポート処理を開始します');

            // バリデーション
            $validator = validator($request->all(), [
                'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
            ], [
                'file.required' => 'ファイルを選択してください。',
                'file.file' => '有効なファイルを選択してください。',
                'file.mimes' => '指定された形式のファイルを選択してください。',
                'file.max' => 'ファイルサイズは10MB以下にしてください。',
            ]);

            if ($validator->fails()) {
                Log::error('Import validation error: ' . json_encode($validator->errors()->toArray()));
                return response()->json([
                    'success' => false,
                    'message' => 'バリデーションエラー',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            // 認証済みユーザーを取得
            $auth = $this->getAuthenticatedUser();
            if (!$auth) {
                Log::error('Import error: Authenticated user not found');
                return response()->json([
                    'success' => false,
                    'message' => '認証情報が見つかりません。',
                    'errors' => ['message' => '認証情報が見つかりません。']
                ], 401);
            }

            $file = $request->file('file');
            if (!$file) {
                Log::error('Import error: File not found in request');
                return response()->json([
                    'success' => false,
                    'message' => 'ファイルが見つかりません。',
                    'errors' => ['message' => 'ファイルが見つかりません。']
                ], 400);
            }

            // ファイルのハッシュを計算
            $fileHash = md5_file($file->getRealPath());
            $lockKey = "import_lock_{$auth->site_id}_{$fileHash}";

            // 重複チェック（5分間のロック）
            if (Cache::has($lockKey)) {
                Log::warning('Duplicate import detected', [
                    'site_id' => $auth->site_id,
                    'file_hash' => $fileHash
                ]);
                return response()->json([
                    'success' => false,
                    'message' => '同じファイルが既にインポート処理中です。',
                    'errors' => ['message' => '重複するインポートは実行できません。']
                ], 409);
            }

            // 5分間のロックを設定
            Cache::put($lockKey, true, now()->addMinutes(5));

            // トランザクション開始
            return DB::transaction(function () use ($file, $auth, $lockKey) {
                try {
                    // ファイルを保存
                    $path = $file->store('imports');
                    if (!$path) {
                        Log::error('Import error: Failed to store file');
                        Cache::forget($lockKey); // ロックを解除
                        return response()->json([
                            'success' => false,
                            'message' => 'ファイルの保存に失敗しました。',
                            'errors' => ['message' => 'ファイルの保存に失敗しました。']
                        ], 500);
                    }

                    // インポートタスクを作成
                    $task = $this->itemImportService->createImportTask(
                        Storage::path($path),
                        $auth->site_id,
                        $auth->login_code
                    );

                    Log::info('インポートタスクを作成しました', ['task_code' => $task->task_code]);

                    // 非同期でインポート処理を開始
                    ProcessItemImport::dispatch($task);

                    Log::info('インポート処理をキューに追加しました');

                    return response()->json([
                        'success' => true,
                        'message' => 'インポート処理を開始しました。',
                        'data' => [
                            'taskCode' => $task->task_code,
                            'redirectUrl' => route('operator.item.import.progress', ['taskCode' => $task->task_code])
                        ]
                    ], 200);

                } catch (\Exception $e) {
                    Cache::forget($lockKey); // エラー時にロックを解除
                    Log::error('Import task creation error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'インポートタスクの作成に失敗しました。',
                        'errors' => ['message' => $e->getMessage()]
                    ], 500);
                }
            });

        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ファイルのアップロードに失敗しました。',
                'errors' => ['message' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * インポート処理の状態を取得
     *
     * @param string $taskCode タスクコード
     * @return JsonResponse
     */
    public function status(string $taskCode): JsonResponse
    {
        try {
            // 認証済みユーザーを取得
            $auth = $this->getAuthenticatedUser();
            if (!$auth) {
                Log::error('Status check error: Authenticated user not found');
                return response()->json([
                    'success' => false,
                    'message' => '認証情報が見つかりません。',
                    'errors' => ['message' => '認証情報が見つかりません。']
                ], 401);
            }

            // サイトIDでタスクを検索
            $task = ImportTask::where('task_code', $taskCode)
                ->where('site_id', $auth->site_id)
                ->first();

            if (!$task) {
                Log::error('Status check error: Task not found', [
                    'task_code' => $taskCode,
                    'site_id' => $auth->site_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'タスクコードが見つかりません。',
                    'errors' => ['message' => 'タスクコードが見つかりません。']
                ], 404);
            }

            // キューワーカーが停止している場合は、pendingステータスを返す
            if ($task->status === 'pending') {
                return response()->json([
                    'success' => true,
                    'message' => 'ステータスを取得しました。',
                    'data' => [
                        'task' => [
                            'status' => 'pending',
                            'statusMessage' => 'インポート処理を待機中です。',
                            'totalRecords' => 0,
                            'processedRecords' => 0,
                            'successRecords' => 0,
                            'errorRecords' => 0,
                            'uploadedAt' => $task->uploaded_at,
                            'importedAt' => null,
                        ],
                        'newRecords' => []
                    ]
                ], 200);
            }

            // 前回のチェック時刻を取得（初回の場合は現在時刻の5分前を設定）
            $lastCheckedAt = Cache::get("last_checked_{$taskCode}", now()->subMinutes(5));

            // 前回のチェックから今回のチェックまでの差分を取得
            $newRecords = ImportTaskRecord::where('import_task_id', $task->id)
                ->where('processed_at', '>', $lastCheckedAt)
                ->orderBy('processed_at', 'asc')
                ->get();

            // 現在時刻を次回の比較用にキャッシュ
            Cache::put("last_checked_{$taskCode}", now(), now()->addHours(1));

            return response()->json([
                'success' => true,
                'message' => 'ステータスを取得しました。',
                'data' => [
                    'task' => [
                        'status' => $task->status,
                        'statusMessage' => $task->error_message ?? 'インポート処理が完了しました。',
                        'totalRecords' => $task->total_records,
                        'processedRecords' => $task->processed_records,
                        'successRecords' => $task->success_records,
                        'errorRecords' => $task->error_records,
                        'uploadedAt' => $task->uploaded_at,
                        'importedAt' => $task->imported_at,
                    ],
                    'newRecords' => $newRecords->map(function ($record) {
                        $data = json_decode($record->data ?? '[]', true);
                        if (!is_array($data)) {
                            $data = [];
                        }

                        return [
                            'rowNumber' => $record->row_number,
                            'itemCode' => $data[0] ?? '', // 商品コード
                            'itemName' => $data[2] ?? '', // 商品名
                            'categoryName' => $data[1] ?? '', // 部門
                            'status' => $record->status,
                            'statusLabel' => $this->getRecordStatusLabel($record->status),
                            'statusClass' => $this->getRecordStatusClass($record->status),
                            'errorMessage' => $record->error_message,
                            'processedAt' => $record->processed_at,
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Status check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ステータスの取得に失敗しました。',
                'errors' => ['message' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * レコードのステータスに応じたラベルクラスを取得
     *
     * @param string $status
     * @return string
     */
    private function getRecordStatusClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * レコードのステータスに応じた日本語表示を取得
     *
     * @param string $status
     * @return string
     */
    private function getRecordStatusLabel($status)
    {
        return match($status) {
            'pending' => '待機中',
            'processing' => '処理中',
            'completed' => '完了',
            'failed' => 'エラー',
            default => '不明',
        };
    }
}
