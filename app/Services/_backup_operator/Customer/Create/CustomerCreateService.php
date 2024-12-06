<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\User;
use App\Models\Authenticate;
use App\Services\Operator\Customer\Create\UserCreationService;
use App\Services\Operator\Customer\Create\AuthenticationCreationService;
use App\Services\Operator\Customer\Create\PhoneNumberFormatter;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Rules\PhoneNumber;

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
     * @return JsonResponse 登録結果
     */
    public function registCustomer(Request $request, $auth): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'user_code' => 'required|string',
            'name' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => ['required', 'string', new PhoneNumber()],
            'phone2' => ['nullable', 'string', new PhoneNumber()],
            'fax' => ['nullable', 'string', new PhoneNumber()],
            'login_code' => 'required|string',
            'password' => 'required|string',
        ], [
            'user_code.required' => 'The user_code field is required.',
            'name.required' => 'The name field is required.',
            'postal_code.required' => 'The postal_code field is required.',
            'phone.required' => 'The phone field is required.',
            'login_code.required' => 'The login_code field is required.',
            'password.required' => 'The password field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'fail',
                'reason' => $validator->errors()->first()
            ], 400);
        }

        try {
            $result = $this->transactionService->execute(function () use ($request, $auth) {
                $phone = $this->phoneNumberFormatter->formatPhoneNumber($request->phone);
                $phone2 = $this->phoneNumberFormatter->formatPhoneNumber($request->phone2 ?? '');
                $fax = $this->phoneNumberFormatter->formatPhoneNumber($request->fax ?? '');
                $password = Hash::make($request->password);

                $userData = [
                    'user_code' => $request->user_code,
                    'site_id' => $auth->site_id,
                    'name' => $request->name,
                    'postal_code' => $request->postal_code,
                    'phone' => $phone,
                    'phone2' => $phone2,
                    'fax' => $fax,
                    'address' => $request->address,
                ];

                $user = $this->userCreationService->createUser($userData, $auth->site_id);

                $authData = [
                    'site_id' => $auth->site_id,
                    'entity_id' => $user->id,
                    'login_code' => $request->login_code,
                    'password' => $password,
                ];

                $this->authenticationCreationService->createAuthenticate($authData);

                return ['message' => 'success', 'login_code' => $request->login_code];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            $this->logService->logError('Customer registration failed: ' . $e->getMessage());
            return response()->json(['message' => 'fail', 'reason' => $e->getMessage()], 500);
        }
    }
}
