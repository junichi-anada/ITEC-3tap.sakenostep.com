<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Authenticate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class CustomerRegistrationService
{
    /**
     * 顧客を登録する
     *
     * @param mixed $request
     * @param mixed $auth
     * @return array
     */
    public function registCustomer($request, $auth)
    {
        try {
            // Additional validation
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
                Log::warning('Customer registration validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                return [
                    'message' => 'error',
                    'reason' => $validator->errors()->first()
                ];
            }

            // Start transaction
            return DB::transaction(function () use ($request, $auth) {
                // Generate user_code
                $user_code = $this->generateUserCode();

                // Create user
                $user = new User();
                $user->user_code = $user_code;
                $user->name = $request->name;
                $user->postal_code = $request->postal_code;
                $user->phone = $request->phone;
                $user->phone2 = $request->phone2;
                $user->fax = $request->fax;
                $user->address = $request->address;
                $user->site_id = $auth->site_id;

                if (!$user->save()) {
                    throw new Exception('ユーザー情報の保存に失敗しました。');
                }

                // Generate login credentials
                $login_code = $user_code; // ログインIDとしてuser_codeを使用
                $password = $this->normalizePhoneNumber($request->phone); // 電話番号をパスワードとして使用
                $auth_code = $this->generateAuthCode();

                // Create authenticate record
                $authenticate = new Authenticate();
                $authenticate->login_code = $login_code;
                $authenticate->password = Hash::make($password); // 電話番号をハッシュ化して保存
                $authenticate->auth_code = $auth_code;
                $authenticate->entity_id = $user->id;
                $authenticate->entity_type = User::class;
                $authenticate->site_id = $auth->site_id;

                if (!$authenticate->save()) {
                    throw new Exception('認証情報の保存に失敗しました。');
                }

                Log::info('Customer registration successful', [
                    'user_id' => $user->id,
                    'user_code' => $user_code,
                    'login_code' => $login_code
                ]);

                return [
                    'message' => 'success',
                    'login_code' => $login_code,
                    'password' => $password // 電話番号をそのまま返す（ユーザーに表示用）
                ];
            });

        } catch (Exception $e) {
            Log::error('Customer registration failed', [
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
     * ユーザーコードを生成
     *
     * @return string
     */
    private function generateUserCode()
    {
        $prefix = 'U';
        $timestamp = now()->format('ym'); // 年月のみを使用
        $random = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT); // 5桁のランダムな数字

        $user_code = $prefix . $timestamp . $random;

        // 重複チェック
        while (User::where('user_code', $user_code)->exists()) {
            $random = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $user_code = $prefix . $timestamp . $random;
        }

        return $user_code;
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
     * エラーメッセージを取得
     *
     * @param string $message
     * @return string
     */
    private function getErrorMessage($message)
    {
        // 既知のエラーメッセージをユーザーフレンドリーなメッセージに変換
        $errorMessages = [
            'ユーザー情報の保存に失敗しました。' => 'ユーザー情報の登録に失敗しました。',
            '認証情報の保存に失敗しました。' => '認証情報の登録に失敗しました。',
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
