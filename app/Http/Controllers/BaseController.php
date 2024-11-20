<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

/**
 * BaseControllerクラス
 *
 * このクラスは認証済みユーザーを取得するための基底コントローラーです。
 *
 * @package App\Http\Controllers
 */
class BaseController extends Controller
{
    /**
     * 認証済みのユーザーを取得します。
     *
     * @return \App\Models\Authenticate 認証されたユーザーのインスタンス
     * @throws \Exception 認証されたユーザーが存在しない場合
     */
    public function getAuthUser(): \App\Models\Authenticate
    {
        $authUser = \Auth::user();

        if ($authUser === null) {
            throw new \Exception('認証されたユーザーが見つかりません。');
        }

        return $authUser;
    }

    /**
     * JSONレスポンスを返す
     *
     * @param string $message
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse($message, $data = [], $status = 200)
    {
        $data['message'] = $message;
        return response()->json($data, $status);
    }

    /**
     * エラーログを出力
     */
    protected function logError(\Exception $e, string $message)
    {
        Log::error($message, ['exception' => $e]);
    }
}
