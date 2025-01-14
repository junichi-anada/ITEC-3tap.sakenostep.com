<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\Authenticate;
use App\Models\LineUser;
use App\Models\Operator;
use App\Models\Order;
use App\Models\User;
use App\Services\Customer\Queries\GetCustomerListQuery;
use App\Services\Customer\CustomerRegistrationService;
use App\Services\Customer\CustomerDeleteService;
use App\Services\Customer\CustomerUpdateService;
use App\Services\Customer\CustomerRestoreService;
use App\Services\Customer\Queries\CustomerSearchQuery;
use App\Contracts\LineMessagingServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    protected $lineMessagingService;

    public function __construct(LineMessagingServiceInterface $lineMessagingService)
    {
        $this->lineMessagingService = $lineMessagingService;
    }

    /**
     * 顧客一覧ページ
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CustomerSearchQuery $searchQuery)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 検索条件がある場合は検索を実行
        if ($request->has(['keyword', 'phone', 'address'])) {
            $customers = $searchQuery->execute($request->all());
        } else {
            // 検索条件がない場合は全件取得
            $customers = (new GetCustomerListQuery())->execute();
        }

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

        return view('operator.customer.regist', compact('operator'));
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
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            // 'postal_code' => 'required|string|max:255',
            // 'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => '顧客名は必須です。',
            'name.string' => '顧客名は文字列で入力してください。',
            'name.max' => '顧客名は255文字以内で入力してください。',
            // 'postal_code.required' => '郵便番号は必須です。',
            // 'postal_code.string' => '郵便番号は文字列で入力してください。',
            // 'postal_code.max' => '郵便番号は255文字以内で入力してください。',
            // 'phone.required' => '電話番号は必須です。',
            // 'phone.string' => '電話番号は文字列で入力してください。',
            // 'phone.max' => '電話番号は255文字以内で入力してください。',
            'address.required' => '住所は必須です。',
            'address.string' => '住所は文字列で入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = Auth::user();

        // user_codeを自動生成
        $request->merge(['user_code' => Str::uuid()]);

        $result = $customerRegistService->registCustomer($request, $auth);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success', 'login_code' => $result['login_code'], 'password' => $result['password']]);
        } else {
            return response()->json(['message' => 'error', 'reason' => $result['reason']], 500);
        }
    }

    /**
     * 顧客詳細情報表示
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 顧客情報を取得（削除済みユーザーも含める）
        $user = User::withTrashed()->findOrFail($id);
        $authenticate = Authenticate::withTrashed()
            ->where('entity_type', User::class)
            ->where('entity_id', $user->id)
            ->first();

        // LINE連携情報を取得
        $lineUser = LineUser::where('user_id', $user->id)->first();

        return view('operator.customer.show', compact('operator', 'user', 'authenticate', 'lineUser'));
    }

    /**
     * 顧客情報編集ページ表示
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 顧客情報を取得
        $user = User::findOrFail($id);
        $authenticate = Authenticate::where('entity_type', User::class)
            ->where('entity_id', $user->id)
            ->first();

        return view('operator.customer.edit', compact('operator', 'user', 'authenticate'));
    }

    /**
     * 顧客情報更新処理
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, CustomerUpdateService $customerUpdateService)
    {
        $auth = Auth::user();
        $user = User::findOrFail($id);

        // バリデーション
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => '顧客名は必須です。',
            'name.string' => '顧客名は文字列で入力してください。',
            'name.max' => '顧客名は255文字以内で入力してください。',
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.string' => '郵便番号は文字列で入力してください。',
            'postal_code.max' => '郵便番号は255文字以内で入力してください。',
            'phone.required' => '電話番号は必須です。',
            'phone.string' => '電話番号は文字列で入力してください。',
            'phone.max' => '電話番号は255文字以内で入力してください。',
            'address.required' => '住所は必須です。',
            'address.string' => '住所は文字列で入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $result = $customerUpdateService->updateCustomer($request, $user, $auth);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['message'] === 'success') {
            return redirect()->route('operator.customer.show', $id)
                ->with('success', '顧客情報を更新しました。');
        } else {
            return redirect()->route('operator.customer.show', $id)
                ->with('error', '顧客情報の更新に失敗しました。');
        }
    }

    /**
     * 顧客情報削除処理
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id, CustomerDeleteService $customerDeleteService)
    {
        $auth = Auth::user();
        $user = User::findOrFail($id);

        $result = $customerDeleteService->deleteCustomer($user, $auth);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['message'] === 'success') {
            return redirect()->route('operator.customer.index')
                ->with('success', '顧客情報を削除しました。');
        } else {
            return redirect()->route('operator.customer.show', $id)
                ->with('error', '顧客情報の削除に失敗しました。');
        }
    }

    /**
     * LINEメッセージ送信処理
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendLineMessage(Request $request)
    {
        Log::info('LINE message request received', [
            'user_code' => $request->user_code,
            'timestamp' => now(),
            'request_id' => uniqid()  // リクエストを識別するためのユニークID
        ]);
        
        // バリデーション
        $validator = validator($request->all(), [
            'user_code' => 'required|string',
            'message' => 'required|string|max:2000',
        ], [
            'user_code.required' => 'ユーザーコードは必須です。',
            'message.required' => 'メッセージは必須です。',
            'message.max' => 'メッセージは2000文字以内で入力してください。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // ユーザー情報の取得
        $user = User::where('user_code', $request->user_code)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ユーザーが見つかりません。'
            ], 404);
        }

        // LINE連携情報の取得
        $lineUser = LineUser::where('user_id', $user->id)->first();
        if (!$lineUser || !$lineUser->is_linked) {
            return response()->json([
                'success' => false,
                'message' => 'LINEが連携されていません。'
            ], 400);
        }

        // メッセージ送信
        try {
            $result = $this->lineMessagingService->pushMessage($lineUser->line_user_id, $request->message);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'メッセージを送信しました。'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'メッセージの送信に失敗しました。'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * 顧客情報を復元する
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request, $id, CustomerRestoreService $customerRestoreService)
    {
        $auth = Auth::user();
        $user = User::withTrashed()->findOrFail($id);

        $result = $customerRestoreService->restoreCustomer($user, $auth);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['message'] === 'success') {
            return redirect()->route('operator.customer.index')
                ->with('success', '顧客情報を復元しました。');
        } else {
            return redirect()->route('operator.customer.show', $id)
                ->with('error', '顧客情報の復元に失敗しました。');
        }
    } 

}
