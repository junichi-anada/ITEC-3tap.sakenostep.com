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
        private HandleFollowEventAction $handleFollowEventAction,
        private HandleTextMessageAction $handleTextMessageAction
    ) {}

    /**
     * Webhookを処理する
     *
     * @param LineWebhookData $data
     * @return void
     * @throws LineMessagingException
     */
    public function execute(LineWebhookData $data): void
    {
        $this->tryCatchWrapper(
            function () use ($data) {
                Log::info('Processing webhook', ['events' => $data->events]);

                // 署名を検証
                $parser = new EventRequestParser(config('services.line.channel_secret'));
                $events = $parser->parse($data->content, $data->signature);

                foreach ($events as $event) {
                    $this->handleEvent($event);
                }
            },
            'Webhookの処理に失敗しました',
            $data->toArray()
        );
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
