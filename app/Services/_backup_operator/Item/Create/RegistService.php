<?php

namespace App\Services\Operator\Item\Create;

use App\Models\User;
use App\Models\Authenticate;
use App\Services\Operator\Item\Log\ItemLogService;
use App\Services\Operator\Item\Transaction\ItemTransactionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/**
 * アイテム登録サービスクラス
 *
 * このクラスは新しいアイテムを登録するためのサービスを提供します。
 */
class RegistService
{
    private ItemLogService $logService;
    private ItemTransactionService $transactionService;

    public function __construct(
        ItemLogService $logService,
        ItemTransactionService $transactionService
    ) {
        $this->logService = $logService;
        $this->transactionService = $transactionService;
    }

    public function registCustomer(Request $request, $auth)
    {
        try {
            return $this->transactionService->execute(function () use ($request, $auth) {
                $loginCode = $this->generateLoginCode();

                // パスワードは電話番号を設定
                $phone = mb_convert_kana($request->phone, 'a');
                $phone = str_replace('-', '', $phone);
                $password = Hash::make($phone);

                // Userモデルにデータを挿入
                $user = User::create([
                    'user_code' => $request->user_code,
                    'site_id' => $auth->site_id,
                    'name' => $request->name,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);

                // 認証情報を挿入
                Authenticate::create([
                    'auth_code' => Str::uuid(),
                    'site_id' => $auth->site_id,
                    'entity_type' => User::class,
                    'entity_id' => $user->id,
                    'login_code' => $loginCode,
                    'password' => $password,
                    'expires_at' => now()->addDays(365),
                ]);

                return ['message' => 'success', 'login_code' => $loginCode, 'password' => $phone];
            });
        } catch (\Exception $e) {
            $this->logService->logError('Customer registration failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }

    /**
     * ログインコードを生成（重複チェック付）
     *
     * @return string
     */
    public function generateLoginCode(): string
    {
        $loginCode = '';
        do {
            $randomNumber = rand(10000, 99999);
            $loginCode = "st" . $randomNumber;
        } while (Authenticate::where('login_code', $loginCode)->exists());

        return $loginCode;
    }
}
