<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\User;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

/**
 * 顧客登録サービスクラス
 *
 * このクラスは新しい顧客を登録するためのサービスを提供します。
 */
final class CustomerCreateService
{
    private $userCreationService;
    private $authenticationCreationService;
    private $phoneNumberFormatter;
    private $logService;
    private $transactionService;

    public function __construct(
        UserCreationService $userCreationService,
        AuthenticationCreationService $authenticationCreationService,
        PhoneNumberFormatter $phoneNumberFormatter,
        CustomerLogService $logService,
        CustomerTransactionService $transactionService
    ) {
        $this->userCreationService = $userCreationService;
        $this->authenticationCreationService = $authenticationCreationService;
        $this->phoneNumberFormatter = $phoneNumberFormatter;
        $this->logService = $logService;
        $this->transactionService = $transactionService;
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
        try {
            return $this->transactionService->execute(function () use ($request, $auth) {
                $loginCode = $this->generateLoginCode();
                $phone = $this->phoneNumberFormatter->formatPhoneNumber($request->phone);
                $password = Hash::make($phone);

                $user = $this->userCreationService->createUser($request, $auth->site_id);
                $this->authenticationCreationService->createAuthenticate($auth->site_id, $user->id, $loginCode, $password);

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
