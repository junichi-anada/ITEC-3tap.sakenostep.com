<?php

namespace App\Http\Controllers\Ajax\Operator;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessItemImport;
use App\Models\ImportTask;
use App\Services\Item\ItemImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ItemImportController extends Controller
{
    private ItemImportService $itemImportService;

    public function __construct(ItemImportService $itemImportService)
    {
        $this->itemImportService = $itemImportService;
    }

    /**
     * インポート処理を実行
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // バリデーション
        $validator = validator($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
        ], [
            'file.required' => 'ファイルを選択してください。',
            'file.file' => '有効なファイルを選択してください。',
            'file.mimes' => 'CSV、TXT、XLS、XLSXファイルを選択してください。',
            'file.max' => 'ファイルサイズは10MB以下にしてください。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $auth = Auth::user();
            if (!$auth) {
                throw new \Exception('認証情報が見つかりません。');
            }

            // インポートタスクを作成
            $task = $this->itemImportService->createImportTask(
                $request->file('file')->getRealPath(),
                $auth->site_id,
                $auth->login_code
            );

            // 最初のジョブをキューに投入（開始行は0、バッチサイズは10）
            ProcessItemImport::dispatch($task, 0, 10);

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
     * インポート処理の進捗確認画面を表示
     *
     * @param string $taskCode タスクコード
     * @return \Illuminate\View\View
     */
    public function progress(string $taskCode)
    {
        $auth = Auth::user();
        
        // タスク情報を取得
        $task = ImportTask::where('task_code', $taskCode)
            ->where('site_id', $auth->site_id)
            ->firstOrFail();

        return view('operator.item.import.progress', [
            'task' => $task,
            'operator' => $auth
        ]);
    }

    /**
     * インポート処理の状態を取得
     *
     * @param string $taskCode タスクコード
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(string $taskCode)
    {
        try {
            $status = $this->itemImportService->getTaskStatus($taskCode);
            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'タスクが見つかりません'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Status check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'ステータス確認に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }
}
