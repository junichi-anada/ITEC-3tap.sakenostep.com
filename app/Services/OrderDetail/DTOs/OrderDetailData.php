<?php

namespace App\Services\OrderDetail\DTOs;

class OrderDetailData
{
    public function __construct(
        public readonly int $userId,
        public readonly int $siteId,
        public readonly ?string $itemCode = null,
        public readonly ?int $volume = null,
        public readonly ?int $orderId = null,
        public readonly ?int $itemId = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            siteId: $data['site_id'],
            itemCode: $data['item_code'] ?? null,
            volume: $data['volume'] ?? null,
            orderId: $data['order_id'] ?? null,
            itemId: $data['item_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'site_id' => $this->siteId,
            'item_code' => $this->itemCode,
            'volume' => $this->volume,
            'order_id' => $this->orderId,
            'item_id' => $this->itemId,
        ];
    }
}
