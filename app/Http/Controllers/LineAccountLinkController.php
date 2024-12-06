<?php

namespace App\Http\Controllers;

use App\Services\Messaging\LineAccountLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineAccountLinkController extends Controller
{
    private LineAccountLinkService $lineAccountLinkService;

    public function __construct(LineAccountLinkService $lineAccountLinkService)
    {
        $this->lineAccountLinkService = $lineAccountLinkService;
    }

    /**
     * LINE連携コールバック
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->input('code');
            $state = $request->input('state');
            $error = $request->input('error');
            $errorDescription = $request->input('error_description');

            if ($error) {
                Log::error('LINE連携エラー: ' . $errorDescription);
                return redirect()->route('line.error')
                    ->with('error', 'LINE連携に失敗しました');
            }

            $result = $this->lineAccountLinkService->handleCallback($code, $state);

            if ($result) {
                return redirect()->route('line.success')
                    ->with('success', 'LINE連携が完了しました');
            }

            return redirect()->route('line.error')
                ->with('error', 'LINE連携に失敗しました');

        } catch (\Exception $e) {
            Log::error('LINE連携コールバックエラー: ' . $e->getMessage());
            return redirect()->route('line.error')
                ->with('error', 'LINE連携処理でエラーが発生しました');
        }
    }

    /**
     * アカウント連携解除
     */
    public function unlink(Request $request)
    {
        try {
            $site = $request->user()->site;
            $lineUserId = $request->input('line_user_id');

            $result = $this->lineAccountLinkService->unlinkAccount($site, $lineUserId);

            if ($result) {
                return response()->json(['message' => 'アカウント連携を解除しました']);
            }

            return response()->json(['error' => 'アカウント連携の解除に失敗しました'], 400);

        } catch (\Exception $e) {
            Log::error('LINE連携解除エラー: ' . $e->getMessage());
            return response()->json(['error' => 'アカウント連携の解除に失敗しました'], 500);
        }
    }
}
