<?php

namespace App\Services\FavoriteItem\DTOs;

class FavoriteItemData
{
    public function __construct(
        public readonly int $userId,
        public readonly int $itemId,
        public readonly int $siteId,
        public readonly ?array $attributes = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            itemId: $data['item_id'],
            siteId: $data['site_id'],
            attributes: $data['attributes'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'item_id' => $this->itemId,
            'site_id' => $this->siteId,
            'attributes' => $this->attributes,
        ], fn($value) => !is_null($value));
    }

    /**
     * 検索条件を取得する
     *
     * @return array
     */
    public function getConditions(): array
    {
        return [
            'user_id' => $this->userId,
            'item_id' => $this->itemId,
            'site_id' => $this->siteId,
        ];
    }
}
