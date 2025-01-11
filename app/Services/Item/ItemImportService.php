<?php

declare(strict_types=1);

namespace App\Services\Item;

use App\Models\ImportTask;
use App\Models\ImportTaskRecord;
use App\Models\Item;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 商品データインポートサービス
 */
class ItemImportService extends BaseService
{
    /**
     * 一度に処理するレコード数
     */
    private const CHUNK_SIZE = 100;

    /**
     * インポートタスクを処理
     *
     * @param ImportTask $task
     * @param array $columns
     * @return bool
     */
    public function processTask(ImportTask $task, array $columns): bool
    {
        try {
            // タスクのステータスを処理中に更新
            $task->status = 'processing';
            $task->save();

            // レコードを処理
            ImportTaskRecord::where('import_task_id', $task->id)
                ->orderBy('row_number')
                ->chunk(self::CHUNK_SIZE, function ($records) use ($task, $columns) {
                    foreach ($records as $record) {
                        try {
                            $this->processRecord($record, $columns, $task);
                        } catch (\Exception $e) {
                            $this->handleRecordError($record, $task, $e);
                        }
                    }
                });

            // 全ての処理が完了したらタスクのステータスを更新
            $this->updateTaskStatus($task);

            return true;
        } catch (\Exception $e) {
            Log::error('インポートタスクの処理中にエラーが発生しました', [
                'task_code' => $task->task_code,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 個別レコードを処理
     *
     * @param ImportTaskRecord $record
     * @param array $columns
     * @param ImportTask $task
     * @throws \Exception
     */
    private function processRecord(ImportTaskRecord $record, array $columns, ImportTask $task): void
    {
        // すでに処理済みのレコードはスキップ
        if ($record->status === ImportTaskRecord::STATUS_COMPLETED) {
            return;
        }

        try {
            DB::beginTransaction();

            // 処理中のステータスに更新
            $record->status = ImportTaskRecord::STATUS_PROCESSING;
            $record->save();

            // レコードデータの取得と検証
            $data = json_decode($record->data, true);
            if (!$this->validateRecordData($data, $columns)) {
                throw new \Exception('データ形式が不正です');
            }

            // 商品コードで既存データを検索
            $item = Item::where('code', $data['code'])
                ->where('site_id', $task->site_id)
                ->first();

            if ($item) {
                // 既存データの更新
                $item->fill($this->formatItemData($data, $columns));
                $item->save();
            } else {
                // 新規データの作成
                $itemData = $this->formatItemData($data, $columns);
                $itemData['site_id'] = $task->site_id;
                Item::create($itemData);
            }

            // 処理完了のステータスに更新
            $record->status = ImportTaskRecord::STATUS_COMPLETED;
            $record->processed_at = now();
            $record->save();

            // タスクの処理件数を更新
            $task->increment('processed_records');
            $task->increment('success_records');

            DB::commit();

            Log::info('レコードの処理が完了しました', [
                'task_code' => $task->task_code,
                'row_number' => $record->row_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * レコードのエラーを処理
     *
     * @param ImportTaskRecord $record
     * @param ImportTask $task
     * @param \Exception $e
     */
    private function handleRecordError(ImportTaskRecord $record, ImportTask $task, \Exception $e): void
    {
        try {
            DB::beginTransaction();

            $record->status = ImportTaskRecord::STATUS_FAILED;
            $record->error_message = $e->getMessage();
            $record->processed_at = now();
            $record->save();

            $task->increment('processed_records');
            $task->increment('error_records');

            DB::commit();

            Log::error('レコードの処理中にエラーが発生しました', [
                'task_code' => $task->task_code,
                'row_number' => $record->row_number,
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('エラー処理中に例外が発生しました', [
                'task_code' => $task->task_code,
                'row_number' => $record->row_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * タスクの最終ステータスを更新
     *
     * @param ImportTask $task
     */
    private function updateTaskStatus(ImportTask $task): void
    {
        $task->refresh();

        if ($task->error_records > 0) {
            $task->status = 'completed_with_errors';
        } else {
            $task->status = 'completed';
        }

        $task->imported_at = now();
        $task->save();
    }

    /**
     * レコードデータを検証
     *
     * @param array $data
     * @param array $columns
     * @return bool
     */
    private function validateRecordData(array $data, array $columns): bool
    {
        foreach ($columns as $column) {
            if ($column['required'] && !isset($data[$column['name']])) {
                return false;
            }
        }
        return true;
    }

    /**
     * 商品データをフォーマット
     *
     * @param array $data
     * @param array $columns
     * @return array
     */
    private function formatItemData(array $data, array $columns): array
    {
        $formatted = [];
        foreach ($columns as $column) {
            if (isset($data[$column['name']])) {
                $formatted[$column['name']] = $data[$column['name']];
            }
        }
        return $formatted;
    }

    /**
     * インポートタスクを作成する
     *
     * @param string $filePath アップロードされたファイルのパス
     * @param int $siteId サイトID
     * @param string $operatorCode オペレーターコード
     * @return ImportTask
     */
    public function createImportTask(string $filePath, int $siteId, string $operatorCode): ImportTask
    {
        return DB::transaction(function () use ($filePath, $siteId, $operatorCode) {
            // ファイルハッシュの生成
            $fileHash = md5_file($filePath);

            // 重複チェック
            if ($this->isDuplicateImport($fileHash, $siteId)) {
                Log::warning('Duplicate import detected', [
                    'site_id' => $siteId,
                    'file_hash' => $fileHash
                ]);
            }

            // インポートタスクの作成
            $task = ImportTask::create([
                'task_code' => 'IMP' . strtoupper(Str::random(8)),
                'site_id' => $siteId,
                'operator_code' => $operatorCode,
                'file_path' => $filePath,
                'file_hash' => $fileHash,
                'status' => ImportTask::STATUS_PENDING,
                'total_records' => 0,
                'processed_records' => 0,
                'success_records' => 0,
                'error_records' => 0
            ]);

            // インポート履歴の保存
            $this->saveImportHistory($fileHash, $siteId);

            // インポート処理をキューに投入
            dispatch(new ProcessItemImport($task));

            return $task;
        });
    }

    /**
     * ファイルの重複チェック
     * @param string $fileHash
     * @param int $siteId
     * @return bool
     */
    private function isDuplicateImport(string $fileHash, int $siteId): bool
    {
        // 過去24時間以内の同一ハッシュのインポート履歴を確認
        $recentImport = DB::table('import_history')
            ->where('file_hash', $fileHash)
            ->where('site_id', $siteId)
            ->where('created_at', '>', now()->subHours(24))
            ->first();

        return $recentImport !== null;
    }

    /**
     * インポート履歴の保存
     * @param string $fileHash
     * @param int $siteId
     * @return void
     */
    private function saveImportHistory(string $fileHash, int $siteId): void
    {
        DB::table('import_history')->insert([
            'file_hash' => $fileHash,
            'site_id' => $siteId,
            'created_at' => now(),
        ]);
    }
}
