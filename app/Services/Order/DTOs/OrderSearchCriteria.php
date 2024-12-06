<?php

declare(strict_types=1);

namespace App\Services\Order\DTOs;

/**
 * 注文検索条件データ転送オブジェクト
 */
final class OrderSearchCriteria
{
    public function __construct(
        public readonly ?int $userId = null,
        public readonly ?int $siteId = null,
        public readonly ?string $orderCode = null,
        public readonly ?bool $isOrdered = null,
        public readonly ?string $orderedFrom = null,
        public readonly ?string $orderedTo = null,
        public readonly array $orderBy = ['created_at' => 'desc']
    ) {}

    /**
     * リクエストデータから検索条件を作成
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            siteId: $data['site_id'] ?? null,
            orderCode: $data['order_code'] ?? null,
            isOrdered: isset($data['is_ordered']) ? (bool) $data['is_ordered'] : null,
            orderedFrom: $data['ordered_from'] ?? null,
            orderedTo: $data['ordered_to'] ?? null,
            orderBy: $data['order_by'] ?? ['created_at' => 'desc']
        );
    }

    /**
     * クエリ条件の配列に変換
     */
    public function toArray(): array
    {
        $conditions = [];

        if ($this->userId !== null) {
            $conditions['user_id'] = $this->userId;
        }

        if ($this->siteId !== null) {
            $conditions['site_id'] = $this->siteId;
        }

        if ($this->orderCode !== null) {
            $conditions['order_code'] = $this->orderCode;
        }

        if ($this->isOrdered !== null) {
            if ($this->isOrdered) {
                $conditions['ordered_at'] = ['not_null' => true];
            } else {
                $conditions['ordered_at'] = null;
            }
        }

        if ($this->orderedFrom !== null) {
            $conditions['ordered_at_from'] = $this->orderedFrom;
        }

        if ($this->orderedTo !== null) {
            $conditions['ordered_at_to'] = $this->orderedTo;
        }

        return $conditions;
    }
}
