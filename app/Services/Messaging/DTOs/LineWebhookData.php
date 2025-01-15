<?php

namespace App\Services\Messaging\DTOs;

use Illuminate\Http\Request;

class LineWebhookData
{
    public function __construct(
        public readonly string $signature,
        public readonly string $content,
        public readonly array $events = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        $signature = $request->header('X-Line-Signature');
        if (empty($signature)) {
            throw LineMessagingException::invalidSignature('Signature header is missing');
        }

        $content = $request->getContent();
        $events = json_decode($content, true)['events'] ?? [];

        return new self(
            content: $content,
            signature: $signature,
            events: $events
        );
    }

    public function toArray(): array
    {
        return [
            'signature' => $this->signature,
            'content' => $this->content,
            'events' => $this->events,
        ];
    }

    /**
     * イベントの種類を取得
     *
     * @param array $event
     * @return string
     */
    public static function getEventType(array $event): string
    {
        return $event['type'] ?? '';
    }

    /**
     * イベントからユーザーIDを取得
     *
     * @param array $event
     * @return string|null
     */
    public static function getUserId(array $event): ?string
    {
        return $event['source']['userId'] ?? null;
    }

    /**
     * イベントからメッセージを取得
     *
     * @param array $event
     * @return string|null
     */
    public static function getMessage(array $event): ?string
    {
        return $event['message']['text'] ?? null;
    }

    /**
     * イベントからリプライトークンを取得
     *
     * @param array $event
     * @return string|null
     */
    public static function getReplyToken(array $event): ?string
    {
        return $event['replyToken'] ?? null;
    }

    /**
     * イベントからノンスを取得
     *
     * @param array $event
     * @return string|null
     */
    public static function getNonce(array $event): ?string
    {
        return $event['link']['nonce'] ?? null;
    }
}
