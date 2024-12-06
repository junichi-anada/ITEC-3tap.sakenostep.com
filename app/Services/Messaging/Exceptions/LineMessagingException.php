<?php

namespace App\Services\Messaging\Exceptions;

use Exception;

class LineMessagingException extends Exception
{
    protected array $errors;

    public function __construct(array $errors = [], string $message = '', int $code = 0, ?Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function invalidSignature(string $signature): self
    {
        return new self(
            ['signature' => __('無効な署名です。')],
            "Invalid signature: {$signature}"
        );
    }

    public static function webhookProcessingFailed(string $reason): self
    {
        return new self(
            ['webhook' => __('Webhookの処理に失敗しました。')],
            "Webhook processing failed: {$reason}"
        );
    }

    public static function messageSendFailed(string $userId, int $httpStatus): self
    {
        return new self(
            ['message' => __('メッセージの送信に失敗しました。')],
            "Failed to send message to user {$userId}. HTTP Status: {$httpStatus}"
        );
    }

    public static function multicastFailed(array $userIds, int $httpStatus): self
    {
        return new self(
            ['multicast' => __('一斉送信に失敗しました。')],
            "Failed to multicast message to users: " . implode(', ', $userIds) . ". HTTP Status: {$httpStatus}"
        );
    }

    public static function profileGetFailed(string $userId, int $httpStatus): self
    {
        return new self(
            ['profile' => __('プロフィールの取得に失敗しました。')],
            "Failed to get profile for user {$userId}. HTTP Status: {$httpStatus}"
        );
    }

    public static function eventHandlingFailed(string $eventType, string $reason): self
    {
        return new self(
            ['event' => __('イベント処理に失敗しました。')],
            "Failed to handle {$eventType} event: {$reason}"
        );
    }
}
