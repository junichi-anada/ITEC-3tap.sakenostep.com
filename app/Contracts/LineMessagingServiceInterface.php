<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\Request;

interface LineMessagingServiceInterface
{
    public function handleWebhook(Request $request): void;
    public function pushMessage(string $userId, string $message): bool;
    public function multicast(array $userIds, string $message): bool;
}
