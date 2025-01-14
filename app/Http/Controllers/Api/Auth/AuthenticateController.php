<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Authenticate;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthenticateController extends Controller
{
    /**
     * オペレータログイン処理
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_code' => 'required|string|max:50',
            'password' => 'required|string|min:8',
            'site_id' => 'required|exists:sites,id',
        ]);

        $authenticate = Authenticate::where([
            'login_code' => $request->login_code,
            'site_id' => $request->site_id,
            'entity_type' => 'operators',
        ])->first();

        if (!$authenticate) {
            return response()->json([
                'status' => 'error',
                'message' => '認証に失敗しました。IDまたはパスワードが正しくありません。'
            ], 401);
        }

        if (!Hash::check($request->password, $authenticate->password)) {
            return response()->json([
                'status' => 'error',
                'message' => '認証に失敗しました。IDまたはパスワードが正しくありません。'
            ], 401);
        }

        if ($authenticate->expires_at && $authenticate->expires_at < now()) {
            return response()->json([
                'status' => 'error',
                'message' => '認証情報の有効期限が切れています。'
            ], 403);
        }

        // サイトの利用権限を確認
        if (!$authenticate->usableSite) {
            return response()->json([
                'status' => 'error',
                'message' => 'このサイトへのアクセス権限がありません。'
            ], 403);
        }

        $token = $authenticate->createToken('auth-token');
        $authenticate->update([
            'token' => $token->plainTextToken,
            'token_expiry' => Carbon::now()->addMinutes(config('sanctum.expiration')),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ログイン成功',
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_in' => config('sanctum.expiration') * 60,
            ]
        ]);
    }

    /**
     * 顧客ログイン処理
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function customerLogin(Request $request)
    {
        $request->validate([
            'login_code' => 'required|string|max:50',
            'password' => 'required|string|min:8',
            'site_id' => 'required|exists:sites,id',
        ]);

        $authenticate = Authenticate::where([
            'login_code' => $request->login_code,
            'site_id' => $request->site_id,
            'entity_type' => 'customers',
        ])->first();

        if (!$authenticate) {
            return response()->json([
                'status' => 'error',
                'message' => '認証に失敗しました。IDまたはパスワードが正しくありません。'
            ], 401);
        }

        if (!Hash::check($request->password, $authenticate->password)) {
            return response()->json([
                'status' => 'error',
                'message' => '認証に失敗しました。IDまたはパスワードが正しくありません。'
            ], 401);
        }

        if ($authenticate->expires_at && $authenticate->expires_at < now()) {
            return response()->json([
                'status' => 'error',
                'message' => '認証情報の有効期限が切れています。'
            ], 403);
        }

        // サイトの利用権限を確認
        if (!$authenticate->usableSite) {
            return response()->json([
                'status' => 'error',
                'message' => 'このサイトへのアクセス権限がありません。'
            ], 403);
        }

        $token = $authenticate->createToken('customer-auth-token');
        $authenticate->update([
            'token' => $token->plainTextToken,
            'token_expiry' => Carbon::now()->addMinutes(config('sanctum.expiration')),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ログイン成功',
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_in' => config('sanctum.expiration') * 60,
            ]
        ]);
    }

    /**
     * ログアウト処理
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->update([
            'token' => null,
            'token_expiry' => null,
        ]);
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ログアウトしました'
        ]);
    }
}
