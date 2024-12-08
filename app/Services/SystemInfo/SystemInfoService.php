<?php

declare(strict_types=1);

namespace App\Services\SystemInfo;

use App\Models\Notification;
use App\Services\SystemInfo\DTOs\SystemInfoData;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SystemInfoService
{
    /**
     * システム情報を取得する
     *
     * @return Collection<SystemInfoData>
     */
    public function getSystemInfo(): Collection
    {
        $now = Carbon::now();

        return Notification::query()
            ->where('publish_start_at', '<=', $now)
            ->where('publish_end_at', '>=', $now)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Notification $notification) {
                return new SystemInfoData(
                    notificationCode: $notification->notification_code,
                    categoryId: $notification->category_id,
                    title: $notification->title,
                    content: $notification->content,
                    publishStartAt: $notification->publish_start_at,
                    publishEndAt: $notification->publish_end_at,
                    createdAt: $notification->created_at
                );
            });
    }
}
