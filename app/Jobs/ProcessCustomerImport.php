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
use App\Models\ImportTaskRecord;

class ProcessCustomerImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ImportTask $task;
    protected int $startRow;
    protected int $batchSize;

    /**
     * 再試行回数
     *
     * @var int
     */
    public $tries = 3;

    /**
     * ジョブのタイムアウト時間（秒）
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param ImportTask $task
     * @param int $startRow
     * @param int $batchSize
     * @return void
     */
    public function __construct(ImportTask $task, int $startRow, int $batchSize)
    {
        $this->task = $task;
        $this->startRow = $startRow;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     *
     * @param CustomerImportService $service
     * @return void
     */
    public function handle(CustomerImportService $service): void
    {
        Log::info('インポートジョブを開始します', [
            'task_code' => $this->task->task_code,
            'start_row' => $this->startRow,
            'batch_size' => $this->batchSize
        ]);

        try {
            // 指定された行から処理を開始
            $service->processFile($this->task, $this->startRow);

        } catch (\Exception $e) {
            Log::error('インポートジョブでエラーが発生しました', [
                'task_code' => $this->task->task_code,
                'start_row' => $this->startRow,
                'error' => $e->getMessage()
            ]);

            // 失敗したレコードのステータスを更新
            ImportTaskRecord::where('import_task_id', $this->task->id)
                ->where('row_number', '>=', $this->startRow)
                ->where('row_number', '<', $this->startRow + $this->batchSize)
                ->update(['status' => ImportTaskRecord::STATUS_FAILED]);

            throw $e;
        }
    }

    /**
     * ジョブが失敗したときの処理
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error('インポートジョブが失敗しました', [
            'task_code' => $this->task->task_code,
            'start_row' => $this->startRow,
            'error' => $exception->getMessage()
        ]);

        // 最大試行回数に達した場合、タスク全体を失敗状態に
        if ($this->attempts() >= $this->tries) {
            $this->task->status = ImportTask::STATUS_FAILED;
            $this->task->error_message = $exception->getMessage();
            $this->task->save();
        }
    }
}
