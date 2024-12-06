<?php

declare(strict_types=1);

namespace App\Services\Order\DTOs;

/**
 * 注文データ転送オブジェクト
 */
final class OrderData
{
    public function __construct(
        public readonly int $siteId,
        public readonly int $userId,
        public readonly ?string $orderCode = null,
        public readonly ?array $items = [],
    ) {}

    /**
     * リクエストデータからDTOを作成
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            siteId: $data['site_id'],
            userId: $data['user_id'],
            orderCode: $data['order_code'] ?? null,
            items: $data['items'] ?? [],
        );
    }

    /**
     * 注文モデル用の配列に変換
     */
    public function toArray(): array
    {
        return [
            'site_id' => $this->siteId,
            'user_id' => $this->userId,
            'order_code' => $this->orderCode,
        ];
    }
}
