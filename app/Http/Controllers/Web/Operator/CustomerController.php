<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\Authenticate;
use App\Models\Operator;
use App\Models\Order;
use App\Models\User;
use App\Services\Customer\Queries\GetCustomerListQuery;
use App\Services\Customer\CustomerRegistrationService;
use App\Services\Customer\CustomerDeleteService;
use App\Services\Customer\CustomerUpdateService;
use App\Services\Customer\Queries\CustomerSearchQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * 顧客一覧ページ
     *
     * @return \Illuminate\View\View
     */
    public function index(GetCustomerListQuery $customerListQuery)
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 検索条件なしで顧客一覧を取得
        $customers = $customerListQuery->execute();

        return view('operator.customer.list', compact('operator', 'customers'));
    }

    /**
     * 顧客手動登録ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // お客様番号をUUIDで生成(仮)
        $customer_code = Str::uuid();

        return view('operator.customer.regist', compact('operator', 'customer_code'));
    }

    /**
     * 顧客手動登録処理
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function store(Request $request, CustomerRegistrationService $customerRegistService)
    {
        // バリデーション
        $request->validate([
            'user_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $auth = Auth::user();

        $result = $customerRegistService->registCustomer($request, $auth);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success', 'login_code' => $result['login_code'], 'password' => $result['password']]);
        } else {
            return response()->json(['message' => $result['message'], 'reason' => $result['reason']], 500);
        }
    }

    /**
     * 顧客データ1件取得
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // userとauthenticatesを結合して取得(未削除のデータ)
        $customer = User::where('users.id', $request->id)
            ->join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->whereNull('users.deleted_at')
            ->where('authenticates.entity_type', User::class)
            ->first();

        if (!$customer) {
            $error_message = '該当する顧客情報が見つかりませんでした。';
            return redirect()->route('operator.customer.error')
                ->with('error_message', $error_message)
                ->with('operator', $operator);
        }

        // 注文履歴をページネーション
        $orders = Order::where('site_id', $auth->site_id)
            ->where('user_id', $customer->id)
            ->orderBy('ordered_at', 'desc')
            ->paginate(10);

        return view('operator.customer.description', compact('customer', 'operator', 'orders'));
    }

    /**
     * 顧客データ1件削除
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, CustomerDeleteService $customerDeleteService, $id)
    {
        $auth = Auth::user();

        $result = $customerDeleteService->deleteCustomer($id);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => $result['reason']], 500);
        }
    }

    /**
     * 検索
     *
     * @return \Illuminate\View\View
     */
    public function search(Request $request, CustomerSearchQuery $customerSearchQuery)
    {
        // バリデーション
        $request->validate([
            'customer_code' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'first_login_date_from' => 'nullable|date',
            'first_login_date_to' => 'nullable|date',
            'last_login_date_from' => 'nullable|date',
            'last_login_date_to' => 'nullable|date',
        ]);

        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 検索条件を取得
        $search = [
            'customer_code' => $request->customer_code,
            'customer_name' => $request->customer_name,
            'customer_address' => $request->customer_address,
            'customer_phone' => $request->customer_phone,
            'first_login_date_from' => $request->first_login_date_from,
            'first_login_date_to' => $request->first_login_date_to,
            'last_login_date_from' => $request->last_login_date_from,
            'last_login_date_to' => $request->last_login_date_to,
        ];

        // 検索条件を元に顧客一覧を取得
        $customers = $customerSearchQuery->execute($search);

        return view('operator.customer.list', compact('operator', 'customers'));
    }

    /**
     * 顧客データ更新
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CustomerUpdateService $customerUpdateService, $id)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $data = $request->only(['name', 'postal_code', 'phone', 'address']);

        $result = $customerUpdateService->updateCustomer($id, $data);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => $result['reason']], 500);
        }
    }
}
