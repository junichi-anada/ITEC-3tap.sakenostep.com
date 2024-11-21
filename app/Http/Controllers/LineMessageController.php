<?php

namespace App\Http\Controllers;

use App\Services\LineMessagingService;

class LineMessageController extends Controller
{
    private $lineService;

    public function __construct(LineMessagingService $lineService)
    {
        $this->lineService = $lineService;
    }

    public function send()
    {
        $userId = 'USER_ID'; // テスト用のLINEユーザーIDを設定
        $message = 'Hello from Laravel!';

        $result = $this->lineService->sendMessage($userId, $message);

        return response()->json(['success' => $result]);
    }
}
