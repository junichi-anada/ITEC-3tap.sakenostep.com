<?php

namespace App\Jobs;

use App\Models\ImportTask;
use App\Services\Customer\CustomerImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessCustomerImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ImportTask $task;

    /**
     * 最大試行回数
     *
     * @var int
     */
    public $tries = 3;

    /**
     * タイムアウトまでの秒数
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(ImportTask $task)
    {
        $this->task = $task;
    }

    /**
     * ジョブの一意のIDを取得
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->task->task_code;
    }

    /**
     * Execute the job.
     */
    public function handle(CustomerImportService $service): void
    {
        try {
            Log::info('インポートジョブを開始します', [
                'task_code' => $this->task->task_code,
                'file_path' => $this->task->file_path,
                'attempt' => $this->attempts()
            ]);

            // ファイルの存在確認
            if (!file_exists($this->task->file_path)) {
                throw new \Exception('インポートファイルが見つかりません: ' . $this->task->file_path);
            }

            // タスクのステータスを処理中に更新
            $this->task->status = 'processing';
            $this->task->status_message = 'インポート処理を実行中です。';
            $this->task->save();

            $service->processFile($this->task);

            Log::info('インポートジョブが完了しました', [
                'task_code' => $this->task->task_code,
                'status' => $this->task->status,
                'total_records' => $this->task->total_records,
                'processed_records' => $this->task->processed_records,
                'success_records' => $this->task->success_records,
                'error_records' => $this->task->error_records
            ]);

        } catch (\Exception $e) {
            Log::error('インポートジョブでエラーが発生しました', [
                'task_code' => $this->task->task_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts()
            ]);

            // タスクのステータスを更新
            $this->task->status = 'failed';
            $this->task->status_message = $e->getMessage();
            $this->task->save();

            throw $e;
        }
    }

    /**
     * ジョブの失敗を処理
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('インポートジョブが失敗しました', [
            'task_code' => $this->task->task_code,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'final_attempt' => $this->attempts()
        ]);

        // タスクのステータスを更新
        $this->task->status = 'failed';
        $this->task->status_message = $exception->getMessage();
        $this->task->save();
    }
}
