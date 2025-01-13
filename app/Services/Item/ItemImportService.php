<?php

namespace App\Services\Item;

use App\Models\Item;
use App\Models\ImportTask;
use App\Models\ImportTaskRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Models\ItemCategory;
use App\Jobs\ProcessItemImport;

class ItemImportService
{
    /**
     * 一度に処理する行数
     */
    const BATCH_SIZE = 100;

    /**
     * インポートタスクを取得
     *
     * @param string $taskCode タスクコード
     * @return ImportTask|null
     */
    public function getImportTask(string $taskCode): ?ImportTask
    {
        try {
            $task = ImportTask::where('task_code', $taskCode)
                ->where('data_type', ImportTask::DATA_TYPE_ITEM)
                ->first();

            if (!$task) {
                Log::warning('Import task not found', [
                    'task_code' => $taskCode,
                    'data_type' => ImportTask::DATA_TYPE_ITEM
                ]);
                return null;
            }

            return $task;
        } catch (\Exception $e) {
            Log::error('Failed to get import task', [
                'task_code' => $taskCode,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * インポートタスクを作成
     *
     * @param string $filePath アップロードされたファイルの一時パス
     * @param int $siteId サイトID
     * @param string $importedBy インポート実行者
     * @return ImportTask
     */
    public function createImportTask(string $filePath, int $siteId, string $importedBy): ImportTask
    {
        try {
            DB::beginTransaction();

            // ファイルを保存し、フルパスを取得
            $storagePath = Storage::disk('local')->putFile(
                'imports/items',
                new File($filePath)
            );

            if (!$storagePath) {
                throw new \Exception('ファイルの保存に失敗しました。');
            }

            // フルパスを取得
            $fullPath = Storage::disk('local')->path($storagePath);

            if (!file_exists($fullPath)) {
                throw new \Exception('保存したファイルが見つかりません。');
            }

            // ヘッダー情報を取得して検証
            $headers = $this->getHeadersFromFile($fullPath);
            if (empty($headers)) {
                throw new \Exception('ヘッダー情報の取得に失敗しました。');
            }

            // インポートタスクを作成
            $task = ImportTask::create([
                'task_code' => 'ITEM_' . strtoupper(Str::random(10)),
                'site_id' => $siteId,
                'data_type' => ImportTask::DATA_TYPE_ITEM,
                'file_path' => $fullPath,
                'status' => ImportTask::STATUS_PENDING,
                'imported_by' => $importedBy,
                'uploaded_at' => now(),
                'total_records' => 0,
                'processed_records' => 0,
                'success_records' => 0,
                'error_records' => 0,
            ]);

            // CSVファイルの場合は、ここで初期データを作成
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if (in_array($extension, ['csv', 'txt'])) {
                $this->processCsvFile($task, 0);
            } else {
                $this->processExcelFile($task, 0);
            }

            DB::commit();
            
            Log::info('インポートタスクを作成しました', [
                'task_code' => $task->task_code,
                'site_id' => $task->site_id,
                'data_type' => $task->data_type,
                'file_path' => $fullPath
            ]);
            
            return $task;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('インポートタスクの作成に失敗しました', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);
            throw $e;
        }
    }

    /**
     * ファイルを読み込んでデータを処理する
     *
     * @param ImportTask $task インポートタスク
     * @param int $startRow 開始行番号（失敗時の再開用）
     * @return void
     */
    public function processFile(ImportTask $task, int $startRow = 0)
    {
        try {
            Log::info('インポートファイルの処理を開始します', [
                'task_code' => $task->task_code,
                'start_row' => $startRow
            ]);

            // バッチサイズ分のレコードを取得して処理
            $records = ImportTaskRecord::where('import_task_id', $task->id)
                ->where('row_number', '>=', $startRow)
                ->where('row_number', '<', $startRow + self::BATCH_SIZE)
                ->orderBy('row_number')
                ->get();

            if ($records->isEmpty()) {
                // 全ての処理が完了した場合
                $this->checkTaskCompletion($task);
                return;
            }

            foreach ($records as $record) {
                try {
                    $this->processRecord($task, $record);
                } catch (\Exception $e) {
                    $record->update([
                        'status' => ImportTaskRecord::STATUS_FAILED,
                        'error_message' => $e->getMessage()
                    ]);
                    $task->increment('error_records');
                    Log::error('レコード処理エラー: ' . $e->getMessage(), [
                        'task_code' => $task->task_code,
                        'row_number' => $record->row_number
                    ]);
                }
            }

            // 次のバッチがあるかチェック
            $nextBatchExists = ImportTaskRecord::where('import_task_id', $task->id)
                ->where('row_number', '>=', $startRow + self::BATCH_SIZE)
                ->exists();

            if ($nextBatchExists) {
                // 次のバッチを処理するジョブをディスパッチ
                ProcessItemImport::dispatch($task, $startRow + self::BATCH_SIZE, self::BATCH_SIZE)
                    ->delay(now()->addSeconds(1));
            } else {
                // 全ての処理が完了した場合
                $this->checkTaskCompletion($task);
            }

        } catch (\Exception $e) {
            Log::error('インポートエラー: ' . $e->getMessage(), [
                'task_code' => $task->task_code,
                'file_path' => $task->file_path,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * ファイルからヘッダー情報を取得
     *
     * @param string $filePath フルパス
     * @return array
     * @throws \Exception
     */
    private function getHeadersFromFile(string $filePath)
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception("ファイルが見つかりません: {$filePath}");
            }

            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (in_array($extension, ['csv', 'txt'])) {
                if (!($handle = fopen($filePath, 'r'))) {
                    throw new \Exception("ファイルを開けません: {$filePath}");
                }
                $headers = fgetcsv($handle);
                fclose($handle);
                return $headers;
            }

            // Excel形式の場合
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $headers = [];
            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $headers[] = $cell->getValue();
                }
            }
            return $headers;

        } catch (\Exception $e) {
            Log::error('ヘッダー情報の取得に失敗しました', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * ヘッダーをマッピング
     *
     * @param array $headers
     * @return array
     */
    private function mapHeaders($headers)
    {
        $columns = [];
        foreach ($headers as $index => $header) {
            $header = trim($header);
            switch ($header) {
                case '商品コード':
                    $columns['code'] = $index;
                    break;
                case '商品名':
                    $columns['name'] = $index;
                    break;
                case '部門':
                    $columns['department'] = $index;
                    break;
            }
        }

        // 必須カラムの存在チェック
        if (!isset($columns['code']) || !isset($columns['name'])) {
            throw new \Exception('必須カラム（商品コード、商品名）が見つかりません');
        }

        return $columns;
    }

    /**
     * レコードを処理する
     *
     * @param ImportTask $task
     * @param ImportTaskRecord $record
     * @throws \Exception
     */
    private function processRecord(ImportTask $task, ImportTaskRecord $record): void
    {
        try {
            DB::beginTransaction();

            $data = json_decode($record->data, true);
            
            // CSVデータから各フィールドを取得
            $itemCode = $data[0] ?? '';  // 商品コード
            $departmentData = $data[1] ?? '';  // 部門
            $itemName = $data[2] ?? '';  // 商品名
            $capacity = $data[3] ?? '';  // 容量
            $quantityPerUnit = $data[4] ?? '';  // ケース入数
            $janCode = $data[5] ?? '';  // バラJANコード

            // 部門コードから数値部分のみを抽出
            $departmentCode = explode(':', $departmentData)[0] ?? '';

            // 部門コードからカテゴリIDを取得
            $category = ItemCategory::where('site_id', $task->site_id)
                ->where('category_code', $departmentCode)
                ->first();

            if (!$category) {
                throw new \Exception("部門コード '{$departmentCode}' に対応するカテゴリが見つかりません。");
            }

            // 商品を作成または更新
            $item = Item::updateOrCreate(
                [
                    'site_id' => $task->site_id,
                    'item_code' => $itemCode,
                ],
                [
                    'category_id' => $category->id,
                    'name' => $itemName,
                    'capacity' => $capacity,  // 容量を追加
                    'quantity_per_unit' => $quantityPerUnit,  // ケース入数を追加
                    'jan_code' => $janCode,  // JANコードを追加
                ]
            );

            // レコードのステータスを更新
            $record->status = ImportTaskRecord::STATUS_COMPLETED;
            $record->processed_at = now();
            $record->save();

            DB::commit();

            // タスクの進捗を更新
            $task->increment('processed_records');
            $task->increment('success_records');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // エラー情報を記録
            $record->status = ImportTaskRecord::STATUS_FAILED;
            $record->error_message = $e->getMessage();
            $record->save();
            
            // タスクの進捗を更新（エラーとして）
            $task->increment('processed_records');
            $task->increment('error_records');

            Log::error('レコード処理エラー: ' . $e->getMessage(), [
                'task_code' => $task->task_code,
                'row_number' => $record->row_number,
                'data' => $data
            ]);

            throw $e;
        }
    }

    /**
     * タスクの完了状態をチェック
     *
     * @param ImportTask $task
     * @return void
     */
    private function checkTaskCompletion(ImportTask $task)
    {
        $pendingCount = ImportTaskRecord::where('import_task_id', $task->id)
            ->where('status', ImportTaskRecord::STATUS_PENDING)
            ->count();

        if ($pendingCount === 0) {
            $task->update([
                'status' => ImportTask::STATUS_COMPLETED,
                'imported_at' => now()
            ]);
        }
    }

    /**
     * タスクの状態を取得
     *
     * @param string $taskCode タスクコード
     * @return array|null
     */
    public function getTaskStatus(string $taskCode): ?array
    {
        $task = ImportTask::where('task_code', $taskCode)->first();
        if (!$task) {
            return null;
        }

        // 最新の10件のレコードを取得
        $records = ImportTaskRecord::where('import_task_id', $task->id)
            ->orderBy('row_number', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($record) {
                $data = json_decode($record->data, true);
                
                return [
                    'rowNumber' => $record->row_number,
                    'itemCode' => $data[0] ?? '',
                    'itemName' => $data[2] ?? '',
                    'categoryName' => $this->extractCategoryName($data[1] ?? ''),
                    'status' => $record->status,
                    'statusLabel' => $this->getStatusLabel($record->status),
                    'statusClass' => $this->getStatusClass($record->status),
                    'processedAt' => $record->processed_at?->format('Y-m-d H:i:s'),
                    'errorMessage' => $record->error_message
                ];
            });

        return [
            'task' => [
                'status' => $task->status,
                'totalRecords' => $task->total_records,
                'processedRecords' => $task->processed_records,
                'successRecords' => $task->success_records,
                'errorRecords' => $task->error_records,
                'progress' => $task->total_records > 0 
                    ? round(($task->processed_records / $task->total_records) * 100, 1) 
                    : 0,
                'isCompleted' => $task->isCompleted(),
            ],
            'records' => $records
        ];
    }

    /**
     * 部門情報からカテゴリ名を抽出
     */
    private function extractCategoryName(?string $department): string
    {
        if (empty($department)) {
            return '';
        }

        $parts = explode(':', $department);
        return trim($parts[1] ?? '');
    }

    /**
     * ステータスラベルを取得
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
     * ステータスクラスを取得
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
     * CSVファイルを処理する
     *
     * @param ImportTask $task インポートタスク
     * @param int $startRow 開始行番号
     * @return void
     */
    private function processCsvFile(ImportTask $task, int $startRow)
    {
        $handle = fopen($task->file_path, 'r');
        if ($handle === false) {
            throw new \Exception('ファイルを開けませんでした。');
        }

        try {
            // ヘッダー行をスキップ
            fgetcsv($handle);
            $rowNumber = 1;
            $totalRecords = 0;

            while (($data = fgetcsv($handle)) !== false) {
                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $rowNumber,
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($data),
                ]);
                $rowNumber++;
                $totalRecords++;
            }

            // タスクの総レコード数を更新
            $task->update(['total_records' => $totalRecords]);

        } finally {
            fclose($handle);
        }
    }

    /**
     * Excelファイルを処理する
     *
     * @param ImportTask $task インポートタスク
     * @param int $startRow 開始行番号
     * @return void
     */
    private function processExcelFile(ImportTask $task, int $startRow)
    {
        $spreadsheet = IOFactory::load($task->file_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rowIterator = $worksheet->getRowIterator();

        // ヘッダー行をスキップ
        $rowIterator->next();
        $rowNumber = 1;
        $totalRecords = 0;

        while ($rowIterator->valid()) {
            $row = $rowIterator->current();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if (!empty(array_filter($rowData))) {
                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $rowNumber,
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($rowData),
                ]);
                $totalRecords++;
            }

            $rowNumber++;
            $rowIterator->next();
        }

        // タスクの総レコード数を更新
        $task->update(['total_records' => $totalRecords]);
    }
}
