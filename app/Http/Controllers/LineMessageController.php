<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\LineMessagingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineMessageController extends Controller
{
    public function __construct(
        private readonly LineMessagingServiceInterface $lineMessagingService
    ) {}

    /**
     * LINEメッセージを送信する
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|string',
                'message' => 'required|string|max:2000'
            ]);

            $result = $this->lineMessagingService->pushMessage(
                $request->input('user_id'),
                $request->input('message')
            );

            if ($result) {
                return response()->json(['message' => 'メッセージを送信しました']);
            }

            return response()->json(['error' => 'メッセージの送信に失敗しました'], 500);
        } catch (\Exception $e) {
            Log::error('Failed to send LINE message: ' . $e->getMessage());
            return response()->json(['error' => 'メッセージの送信中にエラーが発生しました'], 500);
        }
    }
}
