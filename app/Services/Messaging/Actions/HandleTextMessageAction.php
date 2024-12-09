<?php

namespace App\Services\Messaging\Actions;

use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Constants\MessageType;
use LINE\Webhook\Model\MessageEvent;
use Illuminate\Support\Facades\Log;

class HandleTextMessageAction
{
    use ServiceErrorHandler;

    public function __construct(
        private MessagingApiApi $messagingApi
    ) {}

    /**
     * テキストメッセージを処理する
     *
     * @param MessageEvent $event
     * @return void
     * @throws LineMessagingException
     */
    public function execute(MessageEvent $event): void
    {
        $this->tryCatchWrapper(
            function () use ($event) {
                $replyToken = $event->getReplyToken();
                $message = $event->getMessage();
                $text = $message->getText();
                $userId = $event->getSource()->getUserId();

                Log::info("Processing text message", [
                    'user_id' => $userId,
                    'text' => $text
                ]);

                // メッセージの内容に応じた処理を実装
                try {
                    $this->handleMessageContent($text, $replyToken);
                } catch (\Exception $e) {
                    throw LineMessagingException::messageSendFailed(
                        $userId,
                        $e->getCode(),
                        $e->getMessage()
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
     * @return void
     */
    private function handleMessageContent(string $text, string $replyToken): void
    {
        // ここでメッセージの内容に応じた処理を実装
        // 現在はエコーボットとして実装
        $replyMessage = "受信したメッセージ: {$text}";

        $textMessage = (new TextMessage())
            ->setType(MessageType::TEXT)
            ->setText($replyMessage);

        $request = (new ReplyMessageRequest())
            ->setReplyToken($replyToken)
            ->setMessages([$textMessage]);

        $this->messagingApi->replyMessage($request);
    }
}
