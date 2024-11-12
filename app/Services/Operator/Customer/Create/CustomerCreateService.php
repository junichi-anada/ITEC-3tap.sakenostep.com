<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\User;
use App\Models\Authenticate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/**
 * 顧客登録サービスクラス
 *
 * このクラスは新しい顧客を登録するためのサービスを提供します。
 * 顧客情報と認証情報をデータベースに保存します。
 */
final class CustomerCreateService
{
    private $userCreationService;
    private $authenticationCreationService;
    private $phoneNumberFormatter;

    public function __construct(
        UserCreationService $userCreationService,
        AuthenticationCreationService $authenticationCreationService,
        PhoneNumberFormatter $phoneNumberFormatter
    ) {
        $this->userCreationService = $userCreationService;
        $this->authenticationCreationService = $authenticationCreationService;
        $this->phoneNumberFormatter = $phoneNumberFormatter;
    }

    /**
     * 顧客を登録する
     *
     * @param Request $request リクエストオブジェクト
     * @param object $auth 認証情報
     * @return array 登録結果
     */
    public function registCustomer(Request $request, $auth): array
    {
        DB::beginTransaction();
        $loginCode = "";

        try {
            $loginCode = $this->generateLoginCode();

            // パスワードは電話番号を設定
            $phone = $this->phoneNumberFormatter->formatPhoneNumber($request->phone);
            $password = Hash::make($phone);

            // Userモデルにデータを挿入
            $user = $this->userCreationService->createUser($request, $auth->site_id);

            // 認証情報を挿入
            $this->authenticationCreationService->createAuthenticate($auth->site_id, $user->id, $loginCode, $password);

            // すべての操作が成功した場合、トランザクションをコミット
            DB::commit();

            return ['message' => 'success', 'login_code' => $loginCode, 'password' => $phone];
        } catch (\Exception $e) {
            // 例外が発生した場合、トランザクションをロールバック
            DB::rollBack();
            Log::error('Customer registration failed: ' . $e->getMessage());

            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }

    /**
     * ログインコードを生成（重複チェック付）
     *
     * @return string 生成されたログインコード
     */
    private function generateLoginCode(): string
    {
        $loginCode = '';
        do {
            $randomNumber = rand(10000, 99999);
            $loginCode = "st" . $randomNumber;
        } while (Authenticate::where('login_code', $loginCode)->exists());

        return $loginCode;
    }
}
