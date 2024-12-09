<?php

namespace App\Services\Messaging\Actions;

use App\Services\Messaging\DTOs\LineMessageData;
use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Constants\MessageType;
use Illuminate\Support\Facades\Log;

class PushMessageAction
{
    use ServiceErrorHandler;

    public function __construct(
        private MessagingApiApi $messagingApi
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

                $textMessage = (new TextMessage())
                    ->setType(MessageType::TEXT)
                    ->setText($message);

                $request = (new PushMessageRequest())
                    ->setTo($userId)
                    ->setMessages([$textMessage]);

                try {
                    $this->messagingApi->pushMessage($request);
                } catch (\Exception $e) {
                    throw LineMessagingException::messageSendFailed(
                        $userId,
                        $e->getCode(),
                        $e->getMessage()
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
