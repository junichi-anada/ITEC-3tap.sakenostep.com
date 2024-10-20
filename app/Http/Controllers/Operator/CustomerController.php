<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Authenticate;
use App\Models\Operator;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class CustomerController extends Controller
{
    /**
     * 顧客一覧ページ
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        $user_count = User::where('site_id', $auth->site_id)->count();
        $new_user_count = User::where('created_at', '>=', now()->startOfDay())->count();
        $line_user_count = 0;

        // 顧客一覧と認証テーブルを結合して取得
        // ただし、削除されていないデータのみ
        $customers = User::join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->where('users.site_id', $auth->site_id)
            ->select('users.*', 'authenticates.login_code');

        // SQL文を出力
        Log::info($customers->toSql());

        $customers = $customers->get();

        return view('operator.customer.list', compact('operator', 'user_count', 'new_user_count', 'line_user_count', 'customers'));
    }

    /**
     * 顧客手動登録ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function regist()
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
    public function store(Request $request)
    {
        $auth = Auth::user();

        Log::info($request->all());

        try {
            // バリデーション
            $request->validate([
                'user_code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'postal_code' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
            ]);


            // Userモデルにデータを挿入
            $user = User::create([
                'user_code' => $request->user_code,
                'site_id' => $auth->site_id,
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // ランダムの5桁の数字を生成
            $random_number = rand(10000, 99999);
            $login_code = "st" . $random_number;
            // 電話番号をハッシュ化
            $password = Hash::make($request->phone);

            // 認証情報を挿入
            Authenticate::create([
                'auth_code' => Str::uuid(),
                'site_id' => $auth->site_id,
                'entity_type' => User::class,
                'entity_id' => $user->id,
                'login_code' => $login_code,
                'password' => $password,
                'expires_at' => now()->addDays(365),
            ]);

            // JSONで返す
            return response()->json(['message' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
            ->select('users.*', 'authenticates.login_code')
            ->whereNull('users.deleted_at')
            ->first();

        $first_order_date = Order::where('user_id', $customer->id)->orderBy('ordered_at', 'asc')->first();
        $last_order_date = Order::where('user_id', $customer->id)->orderBy('ordered_at', 'desc')->first();

        // 注文履歴をページネーション
        $orders = Order::where('user_id', $customer->id)->orderBy('ordered_at', 'desc')->paginate(10);

        return view('operator.customer.description', compact('customer', 'operator', 'first_order_date', 'last_order_date', 'orders'));
    }


    /**
     * 顧客データ1件削除
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $auth = Auth::user();
    
            // バリデーション
            $request->validate([
                'user_code' => 'required|string|max:255',
            ]);

            // ユーザーを削除
            $user = User::where('user_code', $request->user_code)->delete();

            // 認証情報を削除
            Authenticate::where('login_code', $request->user_code)->delete();
    
            // JSONで返す
            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
