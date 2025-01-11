<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\ImportTask;
use App\Services\Item\ItemImportService;
use App\Services\Customer\CustomerImportService;

/**
 * インポート処理を実行するキュークラス
 */
class ImportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * インポートタスク
     *
     * @var ImportTask
     */
    protected $task;

    /**
     * 最大試行回数
     *
     * @var int
     */
    public $tries = 3;

    /**
     * タイムアウト時間（秒）
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * コンストラクタ
     *
     * @param ImportTask $task
     */
    public function __construct(ImportTask $task)
    {
        $this->task = $task;
    }

    /**
     * ジョブの実行
     *
     * @return void
     */
    public function handle()
    {
        Log::info('インポート処理を開始します', ['task_code' => $this->task->task_code]);

        try {
            // データタイプに応じて適切なサービスを使用
            switch ($this->task->data_type) {
                case ImportTask::DATA_TYPE_ITEM:
                    $service = app(ItemImportService::class);
                    break;
                case ImportTask::DATA_TYPE_CUSTOMER:
                    $service = app(CustomerImportService::class);
                    break;
                default:
                    throw new \Exception('未対応のデータタイプです');
            }

            // インポート処理の実行
            $service->processImport($this->task);

            Log::info('インポート処理が完了しました', ['task_code' => $this->task->task_code]);
        } catch (\Exception $e) {
            Log::error('インポート処理でエラーが発生しました', [
                'task_code' => $this->task->task_code,
                'error' => $e->getMessage()
            ]);

            // タスクを失敗状態に更新
            $this->task->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'imported_at' => now()
            ]);

            throw $e;
        }
    }

    /**
     * 失敗時の処理
     *
     * @param \Exception $e
     * @return void
     */
    public function failed(\Exception $e)
    {
        Log::error('インポートジョブが失敗しました', [
            'task_code' => $this->task->task_code,
            'error' => $e->getMessage(),
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries
        ]);

        // 最大試行回数に達した場合のみ、タスクを失敗状態に更新
        if ($this->attempts() >= $this->tries) {
            $this->task->update([
                'status' => 'failed',
                'error_message' => "最大試行回数({$this->tries}回)を超えました。: " . $e->getMessage(),
                'imported_at' => now()
            ]);
        }
    }
}
