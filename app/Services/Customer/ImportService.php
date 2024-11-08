<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class ImportService
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
                'name' => 'required|string|max:255',
                'postal_code' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
            }
        }

        return $errors;
    }

    /**
     * データをデータベースにインポートするメソッド
     *
     * @param array $data
     * @return bool
     */
    public function importToDatabase(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                Customer::create([
                    'name' => $row['name'],
                    'postal_code' => $row['postal_code'],
                    'phone' => $row['phone'],
                    'address' => $row['address'],
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
