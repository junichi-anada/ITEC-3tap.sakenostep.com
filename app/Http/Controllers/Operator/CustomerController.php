<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Services\Operator\Customer\Create\CustomerCreateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 顧客管理コントローラー
 *
 * このコントローラーは顧客の登録、更新、削除を管理します。
 */
class CustomerController extends Controller
{
    private $customerCreateService;

    public function __construct(CustomerCreateService $customerCreateService)
    {
        $this->customerCreateService = $customerCreateService;
    }

    /**
     * 顧客を登録する
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 認証情報を取得
        $auth = Auth::user();

        // サービスクラスを使用して顧客を登録
        return $this->customerCreateService->registCustomer($request, $auth);
    }

    // 他のメソッド（更新、削除など）もここに追加できます
}
