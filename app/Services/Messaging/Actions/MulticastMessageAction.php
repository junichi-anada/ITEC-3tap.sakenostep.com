<?php

namespace App\Services\Messaging\Actions;

use App\Services\Messaging\DTOs\LineMessageData;
use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\Log;

class MulticastMessageAction
{
    use ServiceErrorHandler;

    public function __construct(
        private LINEBot $bot
    ) {}

    /**
     * 複数のユーザーにメッセージを一斉送信する
     *
     * @param array $userIds
     * @param string $message
     * @return void
     * @throws LineMessagingException
     */
    public function execute(array $userIds, string $message): void
    {
        $this->tryCatchWrapper(
            function () use ($userIds, $message) {
                Log::info("Multicasting message", [
                    'user_count' => count($userIds),
                    'message' => $message
                ]);

                if (empty($userIds)) {
                    Log::warning("No users to multicast message to");
                    return;
                }

                $messageBuilder = new TextMessageBuilder($message);
                $response = $this->bot->multicast($userIds, $messageBuilder);

                if (!$response->isSucceeded()) {
                    throw LineMessagingException::multicastFailed(
                        $userIds,
                        $response->getHTTPStatus()
                    );
                }
            },
            '一斉送信に失敗しました'
        );
    }

    /**
     * DTOを使用して一斉送信する
     *
     * @param array $userIds
     * @param LineMessageData $data
     * @return void
     * @throws LineMessagingException
     */
    public function executeWithDTO(array $userIds, LineMessageData $data): void
    {
        $this->execute($userIds, $data->message);
    }

    /**
     * チャンクに分割して一斉送信する
     * LINEの制限（最大同時送信数）に対応するため
     *
     * @param array $userIds
     * @param string $message
     * @param int $chunkSize
     * @return void
     * @throws LineMessagingException
     */
    public function executeInChunks(array $userIds, string $message, int $chunkSize = 150): void
    {
        $chunks = array_chunk($userIds, $chunkSize);
        
        foreach ($chunks as $chunk) {
            $this->execute($chunk, $message);
        }
    }
}
