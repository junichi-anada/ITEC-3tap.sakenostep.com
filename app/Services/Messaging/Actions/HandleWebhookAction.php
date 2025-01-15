<?php

namespace App\Services\Messaging\Actions;

use App\Services\Messaging\DTOs\LineWebhookData;
use App\Services\Messaging\Exceptions\LineMessagingException;
use App\Services\ServiceErrorHandler;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\FollowEvent;
use LINE\Parser\EventRequestParser;
use Illuminate\Support\Facades\Log;

class HandleWebhookAction
{
    use ServiceErrorHandler;

    public function __construct(
        private MessagingApiApi $messagingApi,
        private HandleTextMessageAction $handleTextMessageAction,
        private HandleFollowEventAction $handleFollowEventAction
    ) {
        // MessagingApiApiの設定を確認
        $config = $this->messagingApi->getConfig();
        if (!$config->getAccessToken()) {
            $config->setAccessToken(config('services.line.channel_access_token'));
        }
    }

    /**
     * Webhookからのリクエストを処理する
     *
     * @param LineWebhookData $data
     * @return void
     * @throws LineMessagingException
     */
    public function execute(LineWebhookData $data): void
    {
        $this->tryCatchWrapper(
            function () use ($data) {
                // 署名の検証
                $channelSecret = config('services.line.channel_secret');
                $signature = $data->signature;
                $content = $data->content;

                if (!$this->validateSignature($channelSecret, $signature, $content)) {
                    throw LineMessagingException::invalidSignature($signature);
                }

                // イベントをパース
                $events = $this->parseEvents($data->content);

                foreach ($events as $event) {
                    $this->handleEvent($event);
                }
            },
            'Webhookの処理に失敗しました',
            $data->toArray()
        );
    }

    /**
     * 署名を検証する
     *
     * @param string $channelSecret
     * @param string $signature
     * @param string $content
     * @return bool
     */
    private function validateSignature(string $channelSecret, string $signature, string $content): bool
    {
        $hash = hash_hmac('sha256', $content, $channelSecret, true);
        $expectedSignature = base64_encode($hash);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * イベントをパースする
     *
     * @param string $content
     * @return array
     */
    private function parseEvents(string $content): array
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            return $data['events'] ?? [];
        } catch (\JsonException $e) {
            throw LineMessagingException::webhookProcessingFailed('Invalid JSON format: ' . $e->getMessage());
        }
    }

    /**
     * イベントを処理する
     *
     * @param \LINE\Webhook\Model\Event $event
     * @return void
     * @throws LineMessagingException
     */
    private function handleEvent($event): void
    {
        try {
            if ($event instanceof MessageEvent && $event->getMessage() instanceof TextMessage) {
                $this->handleTextMessageAction->execute($event);
            } elseif ($event instanceof FollowEvent) {
                $this->handleFollowEventAction->execute($event);
            }
        } catch (\Exception $e) {
            throw LineMessagingException::eventHandlingFailed(
                get_class($event),
                $e->getMessage()
            );
        }
    }
}
