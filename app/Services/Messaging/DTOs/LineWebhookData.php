<?php

namespace App\Services\Messaging\DTOs;

use Illuminate\Http\Request;

class LineWebhookData
{
    public function __construct(
        public readonly string $signature,
        public readonly string $content,
        public readonly array $events
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            signature: $request->header('X-Line-Signature'),
            content: $request->getContent(),
            events: json_decode($request->getContent(), true)['events'] ?? []
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
}
