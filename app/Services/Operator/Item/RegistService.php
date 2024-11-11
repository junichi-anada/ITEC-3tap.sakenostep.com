<?php

namespace App\Services\Operator\Item;

use App\Models\User;
use App\Models\Authenticate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RegistService
{
  public function registCustomer(Request $request, $auth)
  {
    DB::beginTransaction();
    $login_code = "";

    try {
        $login_code = $this->generateLoginCode();

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
            'login_code' => $login_code,
            'password' => $password,
            'expires_at' => now()->addDays(365),
        ]);

        // すべての操作が成功した場合、トランザクションをコミット
        DB::commit();

        return ['message' => 'success', 'login_code' => $login_code, 'password' => $phone];
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
   * @return string
   */
  public function generateLoginCode(): string
  {
    $loginCode = '';
    do {
        $random_number = rand(10000, 99999);
        $loginCode = "st" . $random_number;
    } while (Authenticate::where('login_code', $loginCode)->exists());

    return $loginCode;
  }


}