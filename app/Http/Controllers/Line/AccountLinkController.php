<?php

declare(strict_types=1);

namespace App\Http\Controllers\Line;

use App\Contracts\LineMessagingServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountLinkController extends Controller
{
    public function __construct(
        private LineMessagingServiceInterface $lineMessagingService
    ) {}

    /**
     * LINEアカウント連携のためのリンクトークンを発行
     *
     * @return JsonResponse
     */
    public function issueLinkToken(): JsonResponse
    {
        try {
            $linkToken = $this->lineMessagingService->issueLinkToken();
            return response()->json(['link_token' => $linkToken]);
        } catch (\Exception $e) {
            Log::error('Failed to issue link token: ' . $e->getMessage());
            return response()->json(['error' => 'リンクトークンの発行に失敗しました'], 500);
        }
    }

    /**
     * LINEアカウント連携のコールバック処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function callback(Request $request): JsonResponse
    {
        try {
            $linkToken = $request->input('link_token');
            $userId = Auth::id();

            if (!$linkToken || !$userId) {
                return response()->json(['error' => '無効なリクエストです'], 400);
            }

            $result = $this->lineMessagingService->linkAccount($linkToken, $userId);

            if ($result) {
                return response()->json(['message' => 'アカウント連携が完了しました']);
            }

            return response()->json(['error' => 'アカウント連携に失敗しました'], 400);
        } catch (\Exception $e) {
            Log::error('Failed to link account: ' . $e->getMessage());
            return response()->json(['error' => 'アカウント連携処理中にエラーが発生しました'], 500);
        }
    }

    /**
     * LINEアカウント連携を解除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unlink(Request $request): JsonResponse
    {
        try {
            $userId = $request->input('line_user_id');

            if (!$userId) {
                return response()->json(['error' => '無効なリクエストです'], 400);
            }

            $result = $this->lineMessagingService->unlinkAccount($userId);

            if ($result) {
                return response()->json(['message' => 'アカウント連携を解除しました']);
            }

            return response()->json(['error' => 'アカウント連携の解除に失敗しました'], 400);
        } catch (\Exception $e) {
            Log::error('Failed to unlink account: ' . $e->getMessage());
            return response()->json(['error' => 'アカウント連携解除中にエラーが発生しました'], 500);
        }
    }
}
