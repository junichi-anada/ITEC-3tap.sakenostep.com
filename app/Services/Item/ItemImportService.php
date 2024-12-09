<?php

namespace App\Services\Item;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ImportTask;
use App\Models\ImportTaskRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ItemImportService
{
    /**
     * 一度に処理する行数
     */
    const CHUNK_SIZE = 1;

    /**
     * インポートタスクを作成する
     *
     * @param string $filePath アップロードされたファイルのパス
     * @param int $siteId サイトID
     * @param string $loginCode ログインコード
     * @return ImportTask
     */
    public function createImportTask($filePath, $siteId, $loginCode)
    {
        return DB::transaction(function () use ($filePath, $siteId, $loginCode) {
            return ImportTask::create([
                'task_code' => 'IMP' . Str::random(10),
                'file_path' => $filePath,
                'status' => 'pending',
                'site_id' => $siteId,
                'data_type' => ImportTask::DATA_TYPE_ITEM,
                'total_records' => 0,
                'processed_records' => 0,
                'success_records' => 0,
                'error_records' => 0,
                'error_message' => null,
                'uploaded_at' => now(),
                'imported_by' => $loginCode,
            ]);
        });
    }

    /**
     * ファイルを読み込んでデータを処理する
     *
     * @param ImportTask $task インポートタスク
     * @return void
     */
    public function processFile(ImportTask $task)
    {
        try {
            Log::info('インポートファイルの処理を開始します', ['task_code' => $task->task_code]);
            set_time_limit(300);

            $extension = pathinfo($task->file_path, PATHINFO_EXTENSION);

            if (in_array($extension, ['csv', 'txt'])) {
                $this->processCsvFile($task);
            } else {
                $this->processExcelFile($task);
            }

            if ($task->error_records > 0) {
                $task->status = 'completed_with_errors';
            } else {
                $task->status = 'completed';
            }
            $task->imported_at = now();
            $task->save();

            Log::info('インポートファイルの処理が完了しました', [
                'task_code' => $task->task_code,
                'status' => $task->status,
                'total_records' => $task->total_records,
                'success_records' => $task->success_records,
                'error_records' => $task->error_records
            ]);

        } catch (\Exception $e) {
            Log::error('インポートエラー: ' . $e->getMessage(), [
                'task_code' => $task->task_code,
                'file_path' => $task->file_path,
                'error' => $e->getMessage()
            ]);
            $task->status = 'failed';
            $task->error_message = $e->getMessage();
            $task->save();
            throw $e;
        }
    }

    /**
     * CSVファイルを処理する
     *
     * @param ImportTask $task インポートタスク
     * @return void
     */
    private function processCsvFile(ImportTask $task)
    {
        if (!file_exists($task->file_path)) {
            throw new \Exception('ファイルが見つかりません: ' . $task->file_path);
        }

        $handle = fopen($task->file_path, 'r');
        if ($handle === false) {
            throw new \Exception('ファイルを開けません: ' . $task->file_path);
        }

        try {
            DB::beginTransaction();

            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new \Exception('ヘッダー行を読み取れません');
            }

            // ヘッダーの位置を特定
            $columns = $this->mapHeaders($headers);

            // 総行数をカウント（ヘッダー行を除く）
            $lines = file($task->file_path);
            $totalRows = count($lines) - 1;

            Log::info('CSVファイルの行数を取得しました', [
                'task_code' => $task->task_code,
                'total_rows' => $totalRows
            ]);

            $task->total_records = $totalRows;
            $task->save();

            // ファイルポインタを先頭に戻し、ヘッダー行をスキップ
            rewind($handle);
            fgetcsv($handle);

            // インポートタスクレコードを作成
            $rowNumber = 2;
            while (($data = fgetcsv($handle)) !== false) {
                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $rowNumber,
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($data),
                ]);
                $rowNumber++;
            }

            DB::commit();

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

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            fclose($handle);
        }
    }

    /**
     * Excelファイルを処理する
     *
     * @param ImportTask $task インポートタスク
     * @return void
     */
    private function processExcelFile(ImportTask $task)
    {
        try {
            DB::beginTransaction();

            $spreadsheet = IOFactory::load($task->file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $headers = $worksheet->getRowIterator(1)->current();

            // ヘッダーの位置を特定
            $headerRow = [];
            foreach ($headers->getCellIterator() as $cell) {
                $headerRow[] = $cell->getValue();
            }
            $columns = $this->mapHeaders($headerRow);

            // 総行数を取得（ヘッダー行を除く）
            $totalRows = $worksheet->getHighestRow() - 1;

            $task->total_records = $totalRows;
            $task->save();

            // インポートタスクレコードを作成
            $rowNumber = 2;
            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $rowNumber,
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($rowData),
                ]);
                $rowNumber++;
            }

            DB::commit();

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

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Excelファイルの処理中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * レコードのエラーを処理する
     *
     * @param ImportTaskRecord $record
     * @param ImportTask $task
     * @param \Exception $e
     * @return void
     */
    private function handleRecordError($record, $task, $e)
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
     * ヘッダーの位置をマッピングする
     *
     * @param array $headers ヘッダー行
     * @return array
     */
    private function mapHeaders($headers)
    {
        $columns = [];
        foreach ($headers as $index => $header) {
            $header = trim($header);
            switch ($header) {
                case '商品コード':
                    $columns['item_code'] = $index;
                    break;
                case '部門':
                    $columns['department'] = $index;
                    break;
                case '商品名':
                    $columns['name'] = $index;
                    break;
                case '容量':
                    $columns['capacity'] = $index;
                    break;
                case 'ケース入数':
                    $columns['quantity_per_unit'] = $index;
                    break;
                case 'バラJANコード':
                    $columns['jan_code'] = $index;
                    break;
                case '単価':
                    $columns['unit_price'] = $index;
                    break;
            }
        }

        // 必須カラムの存在チェック
        $requiredColumns = ['item_code', 'name', 'department'];
        foreach ($requiredColumns as $column) {
            if (!isset($columns[$column])) {
                throw new \Exception("必須カラム（{$column}）が見つかりません");
            }
        }

        return $columns;
    }

    /**
     * 半角カタカナを全角カタカナに変換する
     *
     * @param string $str 変換対象の文字列
     * @return string 変換後の文字列
     */
    private function convertHankakuKana($str)
    {
        return mb_convert_kana($str, 'KV');
    }

    /**
     * 部門情報を処理する
     *
     * @param string $department 部門情報（例: "001:食品"）
     * @param int $siteId サイトID
     * @return int カテゴリID
     */
    private function processDepartment($department, $siteId)
    {
        $parts = explode(':', $department);
        if (count($parts) !== 2) {
            throw new \Exception('部門の形式が不正です。（例: 001:食品）');
        }

        $categoryCode = str_pad(trim($parts[0]), 3, '0', STR_PAD_LEFT);
        $categoryName = trim($parts[1]);

        // カテゴリの検索または作成
        $category = ItemCategory::firstOrCreate(
            [
                'site_id' => $siteId,
                'category_code' => $categoryCode,
            ],
            [
                'name' => $categoryName,
                'is_published' => true,
            ]
        );

        return $category->id;
    }

    /**
     * 1行のデータを処理する
     *
     * @param ImportTaskRecord $record インポートタスクレコード
     * @param array $columns カラムのマッピング
     * @param ImportTask $task インポートタスク
     * @return void
     */
    private function processRecord($record, $columns, $task)
    {
        DB::beginTransaction();
        try {
            $record->status = ImportTaskRecord::STATUS_PROCESSING;
            $record->save();

            $data = json_decode($record->data, true);
            if (!is_array($data)) {
                throw new \Exception('データの形式が不正です');
            }

            // データの取得と整形
            $itemCode = isset($columns['item_code']) ? trim($data[$columns['item_code']]) : '';
            $department = isset($columns['department']) ? trim($data[$columns['department']]) : '';
            $name = isset($columns['name']) ? $this->convertHankakuKana(trim($data[$columns['name']])) : '';
            $capacity = isset($columns['capacity']) ? (float)str_replace(',', '', trim($data[$columns['capacity']])) : null;
            $quantityPerUnit = isset($columns['quantity_per_unit']) ? (int)trim($data[$columns['quantity_per_unit']]) : null;
            $janCode = isset($columns['jan_code']) ? trim($data[$columns['jan_code']]) : null;
            $unitPrice = isset($columns['unit_price']) ? (float)str_replace(',', '', trim($data[$columns['unit_price']])) : 0;

            // バリデーション
            $errors = [];
            if (empty($itemCode)) $errors[] = '商品コードは必須です';
            if (empty($name)) $errors[] = '商品名は必須です';
            if (empty($department)) $errors[] = '部門は必須です';
            if ($janCode && strlen($janCode) > 13) $errors[] = 'バラJANコードは13文字以内で入力してください';

            if (!empty($errors)) {
                throw new \Exception(implode(', ', $errors));
            }

            // JANコードの重複チェック
            if ($janCode) {
                $existingItem = Item::where('jan_code', $janCode)
                    ->where('site_id', $task->site_id)
                    ->where('item_code', '!=', $itemCode)
                    ->first();

                if ($existingItem) {
                    throw new \Exception("JANコード '{$janCode}' は既に商品コード '{$existingItem->item_code}' で使用されています");
                }
            }

            // 部門情報の処理
            $categoryId = $this->processDepartment($department, $task->site_id);

            // 商品の作成または更新
            $item = Item::firstOrNew([
                'site_id' => $task->site_id,
                'item_code' => $itemCode
            ]);

            $item->fill([
                'category_id' => $categoryId,
                'name' => $name,
                'capacity' => $capacity,
                'quantity_per_unit' => $quantityPerUnit,
                'jan_code' => $janCode,
                'unit_price' => $unitPrice,
                'from_source' => 'IMPORT',
            ]);
            $item->save();

            $record->status = ImportTaskRecord::STATUS_COMPLETED;
            $record->processed_at = now();
            $record->save();

            $task->increment('processed_records');
            $task->increment('success_records');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * インポートタスクの状態を取得
     *
     * @param string $taskCode タスクコード
     * @return array|null
     */
    public function getTaskStatus($taskCode)
    {
        $task = ImportTask::where('task_code', $taskCode)->first();
        if (!$task) {
            return null;
        }

        $records = ImportTaskRecord::where('import_task_id', $task->id)
            ->orderBy('row_number')
            ->get();

        return [
            'task' => $task,
            'records' => $records
        ];
    }
}
