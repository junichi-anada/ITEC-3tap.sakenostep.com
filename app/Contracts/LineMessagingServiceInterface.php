<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\Request;

interface LineMessagingServiceInterface
{
    public function handleWebhook(Request $request): void;
    public function pushMessage(string $userId, string $message): bool;
    public function multicast(array $userIds, string $message): bool;

    // アカウント連携用のメソッドを追加
    public function getLinkToken(string $userId): ?string;
    public function issueLinkToken(): string;
    public function linkAccount(string $linkToken, int $userId): bool;
    public function unlinkAccount(string $userId): bool;
}
