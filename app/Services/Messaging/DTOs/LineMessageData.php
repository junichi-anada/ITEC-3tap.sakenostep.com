<?php

namespace App\Services\Messaging\DTOs;

class LineMessageData
{
    public function __construct(
        public readonly string $userId,
        public readonly string $message,
        public readonly ?array $options = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            message: $data['message'],
            options: $data['options'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'message' => $this->message,
            'options' => $this->options,
        ], fn($value) => !is_null($value));
    }
}
