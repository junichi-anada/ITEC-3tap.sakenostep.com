<?php

declare(strict_types=1);

namespace App\Services\SystemInfo\DTOs;

use Carbon\Carbon;

/**
 * システム管理者からのお知らせのデータ転送オブジェクト
 */
final class SystemInfoData
{
    public function __construct(
        public readonly string $notificationCode,
        public readonly int $categoryId,
        public readonly string $title,
        public readonly string $content,
        public readonly Carbon $publishStartAt,
        public readonly Carbon $publishEndAt,
        public readonly Carbon $createdAt
    ) {}

    /**
     * 現在公開中かどうかを判定
     */
    public function isPublished(): bool
    {
        $now = Carbon::now();
        return $now->greaterThanOrEqualTo($this->publishStartAt) 
            && $now->lessThanOrEqualTo($this->publishEndAt);
    }
}
