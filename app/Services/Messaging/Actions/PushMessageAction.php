<?php

namespace App\Services\Messaging\Actions;

use App\Services\Messaging\DTOs\LineMessageData;
use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\Log;

class PushMessageAction
{
    use ServiceErrorHandler;

    public function __construct(
        private LINEBot $bot
    ) {}

    /**
     * メッセージを送信する
     *
     * @param string $userId
     * @param string $message
     * @return void
     * @throws LineMessagingException
     */
    public function execute(string $userId, string $message): void
    {
        $this->tryCatchWrapper(
            function () use ($userId, $message) {
                Log::info("Pushing message to user", [
                    'user_id' => $userId,
                    'message' => $message
                ]);

                $messageBuilder = new TextMessageBuilder($message);
                $response = $this->bot->pushMessage($userId, $messageBuilder);

                if (!$response->isSucceeded()) {
                    throw LineMessagingException::messageSendFailed(
                        $userId,
                        $response->getHTTPStatus()
                    );
                }
            },
            'メッセージの送信に失敗しました'
        );
    }

    /**
     * DTOを使用してメッセージを送信する
     *
     * @param LineMessageData $data
     * @return void
     * @throws LineMessagingException
     */
    public function executeWithDTO(LineMessageData $data): void
    {
        $this->execute($data->userId, $data->message);
    }
}
