<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\User;
use App\Models\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * 顧客削除サービスクラス
 *
 * このクラスは顧客を削除するためのサービスを提供します。
 */
class CustomerDeleteService
{
    /**
     * 顧客を削除する
     *
     * @param Request $request リクエストオブジェクト
     * @return JsonResponse 削除結果
     */
    public function deleteCustomer(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'fail',
                'reason' => 'Unauthorized access'
            ], 401);
        }

        $validator = \Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:sites,id',
            'user_code' => 'required|string|exists:users,user_code',
        ], [
            'site_id.required' => 'The site_id field is required.',
            'site_id.integer' => 'The site_id must be an integer.',
            'site_id.exists' => 'The specified site_id does not exist.',
            'user_code.required' => 'The user_code field is required.',
            'user_code.string' => 'The user_code must be a string.',
            'user_code.exists' => 'The specified user_code does not exist.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'fail',
                'reason' => $validator->errors()->first()
            ], 400);
        }

        if (Auth::user()->site_id !== $request->input('site_id')) {
            return response()->json([
                'message' => 'fail',
                'reason' => 'Unauthorized site access'
            ], 403);
        }

        return DB::transaction(function () use ($request) {
            $siteId = $request->input('site_id');
            $userCode = $request->input('user_code');

            $user = User::where('site_id', $siteId)->where('user_code', $userCode)->first();

            if (!$user) {
                return response()->json(['message' => 'fail', 'reason' => 'The specified user_code does not exist.'], 404);
            }

            $user->delete();

            Authenticate::where('site_id', $siteId)->where('entity_id', $user->id)->delete();

            return response()->json(['message' => 'success']);
        });
    }
}
