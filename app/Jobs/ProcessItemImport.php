<?php

namespace App\Jobs;

use App\Models\ImportTask;
use App\Services\Item\ItemImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessItemImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * インポートタスク
     *
     * @var ImportTask
     */
    protected $task;

    /**
     * 試行回数
     *
     * @var int
     */
    public $tries = 1;

    /**
     * タイムアウト時間（秒）
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @param ImportTask $task
     * @return void
     */
    public function __construct(ImportTask $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @param ItemImportService $itemImportService
     * @return void
     */
    public function handle(ItemImportService $itemImportService)
    {
        try {
            Log::info('商品データインポート処理を開始します', [
                'task_code' => $this->task->task_code
            ]);

            // 処理開始時にステータスを更新
            DB::transaction(function () {
                $this->task->status = 'processing';
                $this->task->error_message = null;
                $this->task->save();
            });

            // インポート処理を実行
            $itemImportService->processFile($this->task);

            // 処理完了時にステータスを更新
            DB::transaction(function () {
                if ($this->task->error_records > 0) {
                    $this->task->status = 'completed_with_errors';
                } else {
                    $this->task->status = 'completed';
                }
                $this->task->imported_at = now();
                $this->task->save();
            });

            Log::info('商品データインポート処理が完了しました', [
                'task_code' => $this->task->task_code,
                'status' => $this->task->status,
                'total_records' => $this->task->total_records,
                'success_records' => $this->task->success_records,
                'error_records' => $this->task->error_records
            ]);

        } catch (\Exception $e) {
            Log::error('商品データインポート処理でエラーが発生しました', [
                'task_code' => $this->task->task_code,
                'error' => $e->getMessage()
            ]);

            // エラー時にステータスを更新
            DB::transaction(function () use ($e) {
                $this->task->status = 'failed';
                $this->task->error_message = $e->getMessage();
                $this->task->imported_at = now();
                $this->task->save();
            });

            throw $e;
        }
    }

    /**
     * ジョブの失敗を処理
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error('商品データインポートジョブが失敗しました', [
            'task_code' => $this->task->task_code,
            'error' => $exception->getMessage()
        ]);

        // タスクのステータスを更新
        DB::transaction(function () use ($exception) {
            $this->task->status = 'failed';
            $this->task->error_message = $exception->getMessage();
            $this->task->imported_at = now();
            $this->task->save();
        });
    }
}
