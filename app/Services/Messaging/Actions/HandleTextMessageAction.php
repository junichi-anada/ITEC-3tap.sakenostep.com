<?php

namespace App\Services\Messaging\Actions;

use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use Illuminate\Support\Facades\Log;

class HandleTextMessageAction
{
    use ServiceErrorHandler;

    public function __construct(
        private LINEBot $bot
    ) {}

    /**
     * テキストメッセージを処理する
     *
     * @param TextMessage $event
     * @return void
     * @throws LineMessagingException
     */
    public function execute(TextMessage $event): void
    {
        $this->tryCatchWrapper(
            function () use ($event) {
                $replyToken = $event->getReplyToken();
                $text = $event->getText();
                $userId = $event->getUserId();

                Log::info("Processing text message", [
                    'user_id' => $userId,
                    'text' => $text
                ]);

                // メッセージの内容に応じた処理を実装
                $response = $this->handleMessageContent($text, $replyToken);

                if (!$response->isSucceeded()) {
                    throw LineMessagingException::messageSendFailed(
                        $userId,
                        $response->getHTTPStatus()
                    );
                }
            },
            'テキストメッセージの処理に失敗しました'
        );
    }

    /**
     * メッセージの内容に応じた処理を実装
     *
     * @param string $text
     * @param string $replyToken
     * @return \LINE\LINEBot\Response
     */
    private function handleMessageContent(string $text, string $replyToken)
    {
        // ここでメッセージの内容に応じた処理を実装
        // 現在はエコーボットとして実装
        $replyMessage = "受信したメッセージ: {$text}";

        return $this->bot->replyText($replyToken, $replyMessage);
    }
}
