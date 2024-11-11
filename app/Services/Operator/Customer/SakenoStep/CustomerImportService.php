<?php
/**
 * 酒のステップ用の顧客データインポートサービスクラス
 * 
 * @package App\Services\Customer\SakenoStep
 * @property array $data
 * @property array $errors
 */
namespace App\Services\Customer\SakenoStep;

use App\Models\Authenticate;
use App\Models\ImportTask;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class CustomerImportService
{
    /**
     * ファイルの中身が適切か確認するメソッド
     *
     * @param array $data
     * @return array
     */
    public function validateFileContent(array $data)
    {
        $errors = [];
        foreach ($data as $index => $row) {
            $validator = Validator::make($row, [
                '取引先コード'           => 'required|numeric',
                '取引先名称'             => 'required|string|max:255',
                '郵便番号'               => 'nullable|numeric',
                '都道府県名'             => 'nullable|string|max:255',
                '市区郡町村名称'         => 'nullable|string|max:255',
                '番地'                   => 'nullable|string|max:255',
                '電話番号1_1'            => 'nullable|string|max:255',
                '電話番号2_1'            => 'nullable|string|max:255',
                '検索カナ'               => 'required|string|max:255',
                'FAX番号1_1'             => 'nullable|string|max:255',
                '取引先・掛/現金印字区分' => 'required|numeric',
                '掛/現金区分'            => 'required|numeric|in:0,1',
                '更新日'                 => 'required|numeric',
                '削除日'                 => 'required|numeric',
                '記念日コード1'          => 'nullable|string|max:255',
                '記念日_年_1'            => 'nullable|string|max:255',
                '記念日_月日_1'          => 'nullable|string|max:255',
                '記念日コード2'          => 'nullable|string|max:255',
                '記念日_年_2'            => 'nullable|string|max:255',
                '記念日_月日_2'          => 'nullable|string|max:255',
                '記念日コード3'          => 'nullable|string|max:255',
                '記念日_年_3'            => 'nullable|string|max:255',
                '記念日_月日_3'          => 'nullable|string|max:255',
                '記念日コード4'          => 'nullable|string|max:255',
                '記念日_年_4'            => 'nullable|string|max:255',
                '記念日_月日_4'          => 'nullable|string|max:255',
                '記念日コード5'          => 'nullable|string|max:255',
                '記念日_年_5'            => 'nullable|string|max:255',
                '記念日_月日_5'          => 'nullable|string|max:255',
                '請求書用紙区分'         => 'nullable|numeric',
                'ＤＭ発行区分'           => 'nullable|numeric',
                '店頭売区分'             => 'nullable|numeric',
                'Column1'                => 'nullable|numeric',
                '税区分'                 => 'nullable|numeric',
                '_1'                     => 'nullable|numeric',
                '_2'                     => 'nullable|numeric',
                '_3'                     => 'nullable|numeric',
                '_4'                     => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
            }
        }

        return $errors;
    }

    /**
     * インポートタスクを作成するメソッド
     *
     * @param $file
     * @return ImportTask
     */
    public function createTask($file_path)
    {
        $taskCode = 'import_' . time();

        $auth = Auth::user();
        try {
            DB::beginTransaction();
            $task = ImportTask::create([
                'task_code' => $taskCode,
                'site_id' => $auth->site_id,
                'data_type' => 'customer',
                'file_path' => $file_path,
                'status' => ImportTask::STATUS_PENDING,
                'imported_by' => $auth->id,
                'uploaded_at' => now(),
            ]);
            DB::commit();
            return $task;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * データを整形するメソッド
     *
     * @param array $data
     * @return array
     */
    public function formatData(array $data): array
    {
        try {
            $formattedData = [];
            foreach ($data as $row) {
                $formattedData[] = [
                    'id' => $row['取引先コード'],
                    'name' => $row['取引先名称'],
                    'postal_code' => substr_replace(str_pad($row['郵便番号'], 7, '0', STR_PAD_LEFT), '-', 3, 0),
                    'address' => str_replace([' ', '　'], '', trim($row['都道府県名称'])) . str_replace([' ', '　'], '', trim($row['市区郡町村名称'])) . str_replace([' ', '　'], '', trim($row['番地'])),
                    'phone' => trim($row['電話番号1_1']),
                    'phone2' => trim($row['電話番号2_1']),
                    'fax' => trim($row['FAX番号1_1']),
                ];
            }
            return $formattedData;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * データをデータベースにインポートするメソッド
     *
     * @param array $data
     * @return bool
     */
    public function importToDatabase(array $data)
    {
        $auth = Auth::user();

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                // 過去に登録されているデータがあれば最新情報に更新
                $customer = User::where('site_id', $auth->site_id)
                    ->where('entity_type', User::class)
                    ->where('user_code', $row['id'])
                    ->first();

                if ($customer) {
                    $customer->update([
                        'name' => $row['name'],
                        'postal_code' => $row['postal_code'],
                        'address' => $row['address'],
                        'phone' => $row['phone'],
                        'phone2' => $row['phone'],
                        'fax' => $row['fax'],
                    ]);
                    continue;
                }

                Customer::create([
                    'name' => $row['name'],
                    'postal_code' => $row['postal_code'],
                    'address' => $row['address'],
                    'phone' => $row['phone'],
                    'phone2' => $row['phone'],
                    'fax' => $row['fax'],
                ]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
