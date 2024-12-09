<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Authenticate;
use App\Models\ImportTask;
use App\Models\ImportTaskRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CustomerImportService
{
    /**
     * 一度に処理する行数
     */
    const CHUNK_SIZE = 1; // 1行ずつ処理するように変更

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
                'data_type' => ImportTask::DATA_TYPE_CUSTOMER,
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
            set_time_limit(300); // タイムアウト時間を5分に設定

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
                'processed_records' => $task->processed_records,
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
            $totalRows = count($lines) - 1; // ヘッダー行を除く

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
            $rowNumber = 2; // ヘッダー行の次から開始
            while (($data = fgetcsv($handle)) !== false) {
                // Log::info('インポートタスクレコードを作成します', [
                //     'task_code' => $task->task_code,
                //     'row_number' => $rowNumber
                // ]);

                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $rowNumber,
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($data),
                ]);
                $rowNumber++;
            }

            DB::commit();
            Log::info('インポートタスクレコードの作成が完了しました', [
                'task_code' => $task->task_code,
                'total_records' => $rowNumber - 2
            ]);

            // レコードを処理
            ImportTaskRecord::where('import_task_id', $task->id)
                ->orderBy('row_number')
                ->chunk(self::CHUNK_SIZE, function ($records) use ($task, $columns) {
                    foreach ($records as $record) {
                        try {
                            DB::beginTransaction();
                            $this->processRecord($record, $columns, $task);
                            DB::commit();
                            // Log::info('レコードの処理が完了しました', [
                            //     'task_code' => $task->task_code,
                            //     'row_number' => $record->row_number
                            // ]);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            $this->handleRecordError($record, $task, $e);
                        }
                    }
                });

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSVファイルの処理中にエラーが発生しました', [
                'task_code' => $task->task_code,
                'error' => $e->getMessage()
            ]);
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
            Log::info('Excelファイルの行数を取得しました', [
                'task_code' => $task->task_code,
                'total_rows' => $totalRows
            ]);

            $task->total_records = $totalRows;
            $task->save();

            // インポートタスクレコードを作成
            $rowNumber = 2; // ヘッダー行の次から開始
            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Log::info('インポートタスクレコードを作成します', [
                //     'task_code' => $task->task_code,
                //     'row_number' => $rowNumber
                // ]);

                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $rowNumber,
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($rowData),
                ]);
                $rowNumber++;
            }

            DB::commit();
            Log::info('インポートタスクレコードの作成が完了しました', [
                'task_code' => $task->task_code,
                'total_records' => $rowNumber - 2
            ]);

            // レコードを処理
            ImportTaskRecord::where('import_task_id', $task->id)
                ->orderBy('row_number')
                ->chunk(self::CHUNK_SIZE, function ($records) use ($task, $columns) {
                    foreach ($records as $record) {
                        try {
                            DB::beginTransaction();
                            $this->processRecord($record, $columns, $task);
                            DB::commit();
                            // Log::info('レコードの処理が完了しました', [
                            //     'task_code' => $task->task_code,
                            //     'row_number' => $record->row_number
                            // ]);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            $this->handleRecordError($record, $task, $e);
                        }
                    }
                });

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Excelファイルの処理中にエラーが発生しました', [
                'task_code' => $task->task_code,
                'error' => $e->getMessage()
            ]);
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
                case '取引先コード':
                    $columns['code'] = $index;
                    break;
                case '取引先名':
                    $columns['name'] = $index;
                    break;
                case '住所':
                    $columns['address'] = $index;
                    break;
                case '郵便番号':
                    $columns['postal_code'] = $index;
                    break;
                case '電話１':
                    $columns['phone'] = $index;
                    break;
                case '電話２':
                    $columns['phone2'] = $index;
                    break;
                case 'FAX':
                    $columns['fax'] = $index;
                    break;
            }
        }

        // 必須カラムの存在チェック
        if (!isset($columns['code']) || !isset($columns['name']) || !isset($columns['phone'])) {
            throw new \Exception('必須カラム（取引先コード、取引先名、電話１）が見つかりません');
        }

        return $columns;
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
        // Log::info('レコードの処理を開始します', [
        //     'task_code' => $task->task_code,
        //     'row_number' => $record->row_number
        // ]);

        $record->status = ImportTaskRecord::STATUS_PROCESSING;
        $record->save();

        $data = json_decode($record->data, true);
        if (!is_array($data)) {
            throw new \Exception('データの形式が不正です');
        }

        // データの取得と整形
        $code = isset($columns['code']) ? trim($data[$columns['code']]) : '';
        $name = isset($columns['name']) ? trim($data[$columns['name']]) : '';
        $address = isset($columns['address']) ? trim($data[$columns['address']]) : '';
        $postalCode = isset($columns['postal_code']) ?
            preg_replace('/[^0-9]/', '', trim($data[$columns['postal_code']])) : '';
        $phone = isset($columns['phone']) ?
            preg_replace('/[^0-9]/', '', trim($data[$columns['phone']])) : '';
        $phone2 = isset($columns['phone2']) ?
            preg_replace('/[^0-9]/', '', trim($data[$columns['phone2']])) : '';
        $fax = isset($columns['fax']) ?
            preg_replace('/[^0-9]/', '', trim($data[$columns['fax']])) : '';

        // バリデーション
        $errors = [];
        if (empty($code)) $errors[] = '取引先コードは必須です';
        if (empty($name)) $errors[] = '取引先名は必須です';

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        // ユーザーの作成または更新
        $user = User::firstOrNew([
            'site_id' => $task->site_id,
            'user_code' => $code
        ]);

        $user->fill([
            'name' => $name,
            'postal_code' => $postalCode,
            'address' => $address,
            'phone' => $phone,
            'phone2' => $phone2,
            'fax' => $fax,
        ]);
        $user->save();

        // パスワードの決定
        $password = $this->determinePassword($phone, $phone2, $fax);

        // 認証情報の作成または更新
        $paddedCode = str_pad($code, 5, '0', STR_PAD_LEFT);
        $loginCode = 'U' . Carbon::now()->format('ym') . $paddedCode;
        $authenticate = Authenticate::firstOrNew([
            'site_id' => $task->site_id,
            'entity_type' => User::class,
            'entity_id' => $user->id,
        ]);

        $authenticate->fill([
            'auth_code' => $this->generateAuthCode(),
            'login_code' => $loginCode,
            'password' => Hash::make($password),
        ]);
        $authenticate->save();

        $record->status = ImportTaskRecord::STATUS_COMPLETED;
        $record->processed_at = now();
        $record->save();

        $task->increment('processed_records');
        $task->increment('success_records');

        // Log::info('レコードの処理が完了しました', [
        //     'task_code' => $task->task_code,
        //     'row_number' => $record->row_number,
        //     'user_code' => $code
        // ]);
    }

    /**
     * パスワードを決定する
     *
     * @param string $phone 電話1
     * @param string $phone2 電話2
     * @param string $fax FAX
     * @return string
     */
    private function determinePassword($phone, $phone2, $fax)
    {
        if (!empty($phone)) {
            return $phone;
        }

        if (!empty($phone2)) {
            return $phone2;
        }

        if (!empty($fax)) {
            return $fax;
        }

        // ランダムな6桁の数値を生成
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * 認証コードを生成
     *
     * @return string
     */
    private function generateAuthCode()
    {
        $prefix = 'A';
        $timestamp = now()->format('ymd');
        $random = strtoupper(Str::random(4));

        $auth_code = $prefix . $timestamp . $random;

        // 重複チェック
        while (Authenticate::where('auth_code', $auth_code)->exists()) {
            $random = strtoupper(Str::random(4));
            $auth_code = $prefix . $timestamp . $random;
        }

        return $auth_code;
    }

    /**
     * インポートタスクの状態を取得
     *
     * @param string $taskCode タスクコード
     * @return array
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
