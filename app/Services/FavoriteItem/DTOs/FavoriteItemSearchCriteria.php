<?php

namespace App\Services\FavoriteItem\DTOs;

class FavoriteItemSearchCriteria
{
    public function __construct(
        public readonly int $userId,
        public readonly ?int $siteId = null,
        public readonly ?array $orderBy = ['created_at' => 'desc'],
        public readonly ?array $with = [],
        public readonly bool $withTrashed = false
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            siteId: $data['site_id'] ?? null,
            orderBy: $data['order_by'] ?? ['created_at' => 'desc'],
            with: $data['with'] ?? [],
            withTrashed: $data['with_trashed'] ?? false
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'site_id' => $this->siteId,
            'order_by' => $this->orderBy,
            'with' => $this->with,
            'with_trashed' => $this->withTrashed,
        ], fn($value) => !is_null($value));
    }

    /**
     * 検索条件を取得する
     *
     * @return array
     */
    public function getConditions(): array
    {
        $conditions = ['user_id' => $this->userId];

        if ($this->siteId !== null) {
            $conditions['site_id'] = $this->siteId;
        }

        return $conditions;
    }

    /**
     * 検索オプションを取得する
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'orderBy' => $this->orderBy,
            'with' => $this->with,
            'containTrash' => $this->withTrashed,
        ];
    }
}
