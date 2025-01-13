<?php

namespace App\Services\Customer;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\Authenticate;

class CustomerUpdateService
{
    /**
     * 顧客情報更新サービスクラス
     *
     * このクラスは顧客情報を更新するためのサービスを提供します。
     * 顧客情報の更新時に、�話番号を基にパスワードも更新します。
     */
    /**
     * 顧客情報を更新する
     *
     * @param mixed $request リクエストデータ
     * @param User $user 更新対象ユーザー
     * @param mixed $auth 認証情報
     * @return array 処理結果
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

                // 認証情報を取得
                $authenticate = Authenticate::where('entity_type', User::class)
                    ->where('entity_id', $user->id)
                    ->first();

                if (!$authenticate) {
                    throw new Exception('認証情報が見つかりません。');
                }

                // パスワードを更新（電話番号の優先順位: phone > phone2 > fax）
                $phoneForPassword = $this->determinePhoneForPassword($request->phone, $request->phone2, $request->fax);
                if ($phoneForPassword) {
                    $normalizedPhone = $this->normalizePhoneNumber($phoneForPassword);
                    $authenticate->password = Hash::make($normalizedPhone);
                    
                    if (!$authenticate->save()) {
                        throw new Exception('認証情報の更新に失敗しました。');
                    }
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
                'request' => $request->all()
            ]);

            return [
                'message' => 'error',
                'reason' => $this->getErrorMessage($e->getMessage())
            ];
        }
    }

    /**
     * パスワードとして使用する電話番号を決定する
     *
     * @param string|null $phone 電話番号
     * @param string|null $phone2 電話番号2
     * @param string|null $fax FAX番号
     * @return string|null
     */
    private function determinePhoneForPassword($phone, $phone2, $fax)
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
        return null;
    }

    /**
     * 電話番号を正規化する（ハイフンを削除）
     *
     * @param string $phone
     * @return string
     */
    private function normalizePhoneNumber($phone)
    {
        return str_replace('-', '', $phone);
    }

    /**
     * エラメッセージを取得
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
