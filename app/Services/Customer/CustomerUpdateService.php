<?php

namespace App\Services\Customer;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class CustomerUpdateService
{
    /**
     * 顧客情報を更新する
     *
     * @param mixed $request
     * @param User $user
     * @param mixed $auth
     * @return array
     */
    public function updateCustomer($request, $user, $auth)
    {
        try {
            // バリデーション
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'regex:/^[0-9\-]+$/'],
                'phone2' => ['nullable', 'regex:/^[0-9\-]+$/'],
                'fax' => ['nullable', 'regex:/^[0-9\-]+$/'],
                'postal_code' => ['required', 'regex:/^\d{3}-?\d{4}$/'],
                'address' => ['required', 'string', 'max:255'],
            ], [
                'name.required' => '顧客名は必須です。',
                'name.string' => '顧客名は文字列で入力してください。',
                'name.max' => '顧客名は255文字以内で入力してください。',
                'phone.required' => '電話番号は必須です。',
                'phone.regex' => '電話番号の形式が正しくありません。',
                'phone2.regex' => '電話番号2の形式が正しくありません。',
                'fax.regex' => 'FAX番号の形式が正しくありません。',
                'postal_code.required' => '郵便番号は必須です。',
                'postal_code.regex' => '郵便番号の形式が正しくありません。',
                'address.required' => '住所は必須です。',
                'address.string' => '住所は文字列で入力してください。',
                'address.max' => '住所は255文字以内で入力してください。',
            ]);

            if ($validator->fails()) {
                Log::warning('Customer update validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all(),
                    'user_id' => $user->id
                ]);
                return [
                    'message' => 'error',
                    'reason' => $validator->errors()->first()
                ];
            }

            // トランザクション開始
            return DB::transaction(function () use ($request, $user, $auth) {
                // ユーザー情報を更新
                $user->name = $request->name;
                $user->postal_code = $request->postal_code;
                $user->phone = $request->phone;
                $user->phone2 = $request->phone2;
                $user->fax = $request->fax;
                $user->address = $request->address;

                if (!$user->save()) {
                    throw new Exception('ユーザー情報の更新に失敗しました。');
                }

                Log::info('Customer update successful', [
                    'user_id' => $user->id,
                    'user_code' => $user->user_code
                ]);

                return [
                    'message' => 'success'
                ];
            });

        } catch (Exception $e) {
            Log::error('Customer update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'user_id' => $user->id
            ]);

            return [
                'message' => 'error',
                'reason' => $this->getErrorMessage($e->getMessage())
            ];
        }
    }

    /**
     * エラーメッセージを取得
     *
     * @param string $message
     * @return string
     */
    private function getErrorMessage($message)
    {
        // 既知のエラーメッセージをユーザーフレンドリーなメッセージに変換
        $errorMessages = [
            'ユーザー情報の更新に失敗しました。' => 'ユーザー情報の更新に失敗しました。',
            'Duplicate entry' => 'この情報は既に登録されています。',
        ];

        foreach ($errorMessages as $key => $value) {
            if (strpos($message, $key) !== false) {
                return $value;
            }
        }

        // 未知のエラーの場合
        return 'システムエラーが発生しました。管理者に連絡してください。';
    }
}
