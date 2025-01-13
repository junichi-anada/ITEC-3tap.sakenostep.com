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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Jobs\ProcessCustomerImport;
use App\Services\Customer\Formatter\CustomerDataFormatter;

class CustomerImportService
{
    /**
     * 一度に処理する行数
     */
    const BATCH_SIZE = 5;  // バッチサイズを小さくする

    private CustomerDataFormatter $formatter;

    public function __construct(CustomerDataFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

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
                ->where('data_type', ImportTask::DATA_TYPE_CUSTOMER)
                ->first();

            if (!$task) {
                Log::warning('Import task not found', [
                    'task_code' => $taskCode,
                    'data_type' => ImportTask::DATA_TYPE_CUSTOMER
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
                'imports/customers',
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

            // インポートタスクを作成（フルパスを保存）
            $task = ImportTask::create([
                'task_code' => 'IMP' . strtoupper(Str::random(10)),
                'site_id' => $siteId,
                'data_type' => ImportTask::DATA_TYPE_CUSTOMER,
                'file_path' => $fullPath, // フルパスを保存
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
                    // エラー処理...
                }
            }

            // 次のバッチがあるかチェック
            $nextBatchExists = ImportTaskRecord::where('import_task_id', $task->id)
                ->where('row_number', '>=', $startRow + self::BATCH_SIZE)
                ->exists();

            if ($nextBatchExists) {
                // 次のバッチを処理するジョブをディスパッチ
                ProcessCustomerImport::dispatch($task, $startRow + self::BATCH_SIZE, self::BATCH_SIZE)
                    ->delay(now()->addSeconds(1));
            } else {
                // 全ての処理が完了した場合
                $this->checkTaskCompletion($task);
            }

        } catch (\Exception $e) {
            // エラー処理...
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

                try {
                    $headers = fgetcsv($handle);
                    if ($headers === false) {
                        throw new \Exception('ヘッダー行を読み取れません');
                    }
                    return $headers;
                } finally {
                    fclose($handle);
                }
            } else {
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $headerRow = [];
                foreach ($worksheet->getRowIterator(1)->current()->getCellIterator() as $cell) {
                    $headerRow[] = $cell->getValue();
                }
                return $headerRow;
            }
        } catch (\Exception $e) {
            Log::error('ヘッダー情報の取得に失敗しました', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
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
        $totalProcessed = $task->processed_records;
        $totalRecords = $task->total_records;

        if ($totalProcessed >= $totalRecords) {
            if ($task->error_records > 0) {
                $task->status = ImportTask::STATUS_COMPLETED_WITH_ERRORS;
            } else {
                $task->status = ImportTask::STATUS_COMPLETED;
            }
            $task->save();

            Log::info('インポートタスクが完了しました', [
                'task_code' => $task->task_code,
                'status' => $task->status,
                'total_records' => $totalRecords,
                'success_records' => $task->success_records,
                'error_records' => $task->error_records
            ]);
        }
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
            throw new \Exception('ファイルを開けません: ' . $task->file_path);
        }

        try {
            // ヘッダー行を読み込む
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new \Exception('ヘッダー行を読み取れません');
            }

            // ヘッダーの位置を特定
            $columns = $this->mapHeaders($headers);

            // 総行数をカウント（ヘッダー行を除く）
            $totalRows = count(file($task->file_path)) - 1;
            
            // まだ総行数が設定されていない場合のみ更新
            if ($task->total_records === 0) {
                $task->total_records = $totalRows;
                $task->save();
            }

            // 指定された開始行まで移動
            for ($i = 0; $i < $startRow; $i++) {
                fgetcsv($handle);
            }

            // 10件ずつ処理
            $records = [];
            $currentRow = $startRow;
            
            while (($data = fgetcsv($handle)) !== false) {
                $records[] = [
                    'row_number' => $currentRow + 2, // ヘッダー行を考慮して+2
                    'data' => $data
                ];
                $currentRow++;

                // 10件たまったらジョブを作成
                if (count($records) >= 10) {
                    $this->dispatchImportJob($task, $records, $columns);
                    $records = [];
                }
            }

            // 残りのレコードがあればジョブを作成
            if (!empty($records)) {
                $this->dispatchImportJob($task, $records, $columns);
            }

        } finally {
            fclose($handle);
        }
    }

    /**
     * インポートジョブを作成してディスパッチする
     *
     * @param ImportTask $task
     * @param array $records
     * @param array $columns
     * @return void
     */
    private function dispatchImportJob(ImportTask $task, array $records, array $columns)
    {
        try {
            DB::beginTransaction();

            // インポートタスクレコードを作成
            foreach ($records as $record) {
                ImportTaskRecord::create([
                    'import_task_id' => $task->id,
                    'row_number' => $record['row_number'],
                    'status' => ImportTaskRecord::STATUS_PENDING,
                    'data' => json_encode($record['data']),
                ]);
            }

            DB::commit();

            // ジョブをディスパッチ
            ProcessCustomerImport::dispatch($task, $records[0]['row_number'], count($records));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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
        try {
            $spreadsheet = IOFactory::load($task->file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // 総行数を取得（ヘッダー行を除く）
            $totalRows = $worksheet->getHighestRow() - 1;
            
            Log::info('Excelファイルの行数を取得しました', [
                'task_code' => $task->task_code,
                'total_rows' => $totalRows
            ]);

            // タスクの総レコード数を更新
            $task->total_records = $totalRows;
            $task->save();

            // ヘッダー行を取得
            $headers = [];
            foreach ($worksheet->getRowIterator(1)->current()->getCellIterator() as $cell) {
                $headers[] = $cell->getValue();
            }
            $columns = $this->mapHeaders($headers);

            // データを5件ずつ処理
            $records = [];
            $currentRow = $startRow + 2; // ヘッダー行を考慮して+2

            // 2行目から処理開始（1行目はヘッダー）
            for ($row = $currentRow; $row <= $totalRows + 1; $row++) {
                $rowData = [];
                foreach ($worksheet->getRowIterator($row)->current()->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $records[] = [
                    'row_number' => $row,
                    'data' => $rowData
                ];

                // 5件たまったらジョブを作成
                if (count($records) >= 5) {
                    $this->dispatchImportJob($task, $records, $columns);
                    $records = [];
                }
            }

            // 残りのレコードがあればジョブを作成
            if (!empty($records)) {
                $this->dispatchImportJob($task, $records, $columns);
            }

            Log::info('インポートタスクレコードの作成が完了しました', [
                'task_code' => $task->task_code,
                'total_records' => $totalRows
            ]);

        } catch (\Exception $e) {
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
    private function processRecord(ImportTask $task, ImportTaskRecord $record)
    {
        try {
            DB::beginTransaction();

            $record->status = ImportTaskRecord::STATUS_PROCESSING;
            $record->save();

            $data = json_decode($record->data, true);
            if (!is_array($data)) {
                throw new \Exception('データの形式が不正です');
            }

            // ヘッダーの位置を取得
            $headers = $this->getHeadersFromFile($task->file_path);
            $columns = $this->mapHeaders($headers);

            // データの取得と整形（より厳密なバリデーション）
            $code = isset($columns['code']) ? trim($data[$columns['code']]) : '';
            $name = isset($columns['name']) ? trim($data[$columns['name']]) : '';
            $address = isset($columns['address']) ? trim($data[$columns['address']]) : '';
            
            // 電話番号の整形
            $phone = isset($columns['phone']) ? 
                $this->formatter->formatPhoneNumber(trim($data[$columns['phone']])) : '';
            $phone2 = isset($columns['phone2']) ? 
                $this->formatter->formatPhoneNumber(trim($data[$columns['phone2']])) : '';
            $fax = isset($columns['fax']) ? 
                $this->formatter->formatPhoneNumber(trim($data[$columns['fax']])) : '';
            
            // 郵便番号の整形
            $postalCode = isset($columns['postal_code']) ? 
                $this->formatter->formatPostalCode(trim($data[$columns['postal_code']])) : '';

            // パスワードの生成
            try {
                $password = $this->formatter->generatePasswordFromPhone($phone, $phone2, $fax);
            } catch (\Exception $e) {
                // Log::warning('Password generation failed', [
                //     'error' => $e->getMessage(),
                //     'data' => $data
                // ]);
                // パスワード生成失敗時は、「R」+6桁の数字をパスワードとして生成
                $randomNumbers = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $randomPassword = 'R' . $randomNumbers;
                $password = Hash::make($randomPassword);
                // 電話番号としても保存
                $phone = $randomPassword;
                // Log::info('Random password generated for customer', [
                //     'code' => $code,
                //     'name' => $name,
                //     'random_password' => $randomPassword
                // ]);
            }

            // バリデーション
            $errors = [];
            if (empty($code)) $errors[] = '取引先コードは必須です';
            if (empty($name)) $errors[] = '取引先名は必須です';
            if (!empty($phone) && strlen($phone) > 20) $errors[] = '電話番号1は20文字以内で入力してください';
            if (!empty($phone2) && strlen($phone2) > 20) $errors[] = '電話番号2は20文字以内で入力してください';
            if (!empty($fax) && strlen($fax) > 20) $errors[] = 'FAX番号は20文字以内で入力してください';

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

            // 認証情報の作成または更新
            $authenticate = $this->createOrUpdateAuthentication($user, $code, $task->site_id, $password);

            $record->status = ImportTaskRecord::STATUS_COMPLETED;
            $record->processed_at = now();
            $record->save();

            DB::commit();

            $task->increment('processed_records');
            $task->increment('success_records');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createOrUpdateAuthentication($user, $code, $siteId, $password)
    {
        $paddedCode = str_pad($code, 5, '0', STR_PAD_LEFT);
        $loginCode = 'U' . Carbon::now()->format('ym') . $paddedCode;
        
        $authenticate = Authenticate::firstOrNew([
            'site_id' => $siteId,
            'entity_type' => User::class,
            'entity_id' => $user->id,
        ]);

        // パスワードの決定（電話番号からのパスワード生成に失敗した場合のフォールバック）
        // $password = $this->determinePassword($user->phone, $user->phone2, $user->fax);
        // if (empty($password)) {
        //     $randomNumbers = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        //     $randomPassword = 'R' . $randomNumbers;
        //     $password = $randomPassword;
        //     // ユーザーの電話番号も更新
        //     $user->phone = $randomPassword;
        //     $user->save();
        //     Log::info('Fallback random password generated for authentication', [
        //         'user_code' => $user->user_code,
        //         'name' => $user->name,
        //         'random_password' => $randomPassword
        //     ]);
        // }

        $authenticate->fill([
            'auth_code' => $this->generateAuthCode(),
            'login_code' => $loginCode,
            'password' => $password,
        ]);

        $authenticate->save();
        return $authenticate;
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

    private function formatData($data, $columns): array
    {
        // データの取得と整形（より厳密なバリデーション）
        $code = isset($columns['code']) ? trim($data[$columns['code']]) : '';
        $name = isset($columns['name']) ? trim($data[$columns['name']]) : '';
        $address = isset($columns['address']) ? trim($data[$columns['address']]) : '';
        
        // 電話番号の整形
        $phone = isset($columns['phone']) ? 
            $this->formatter->formatPhoneNumber(trim($data[$columns['phone']])) : '';
        $phone2 = isset($columns['phone2']) ? 
            $this->formatter->formatPhoneNumber(trim($data[$columns['phone2']])) : '';
        $fax = isset($columns['fax']) ? 
            $this->formatter->formatPhoneNumber(trim($data[$columns['fax']])) : '';
            
        // 郵便番号の整形
        $postalCode = isset($columns['postal_code']) ? 
            $this->formatter->formatPostalCode(trim($data[$columns['postal_code']])) : '';

        // パスワードの生成
        try {
            $password = $this->formatter->generatePasswordFromPhone($phone, $phone2, $fax);
        } catch (\Exception $e) {
            // Log::warning('Password generation failed', [
            //     'error' => $e->getMessage(),
            //     'data' => $data
            // ]);
            $password = null;
        }

        return [
            'code' => $code,
            'name' => $name,
            'address' => $address,
            'postal_code' => $postalCode,
            'phone' => $phone,
            'phone2' => $phone2,
            'fax' => $fax,
            'password' => $password
        ];
    }
}
