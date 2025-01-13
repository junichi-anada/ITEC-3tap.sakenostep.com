<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\Request;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;

interface LineMessagingServiceInterface
{
    public function handleWebhook(Request $request): void;
    public function pushMessage(string $userId, string $message): bool;
    public function multicast(array $userIds, string $message): bool;

    // アカウント連携用のメソッドを追加
    public function getLinkToken(string $userId): ?string;
    /**
     * リンクトークンを発行する
     *
     * @param string $userId LINEユーザーID
     * @return string
     */
    public function issueLinkToken(string $userId): string;
    public function linkAccount(string $linkToken, int $userId): bool;
    public function unlinkAccount(string $userId): bool;

    /**
     * リプライメッセージを送信する
     *
     * @param ReplyMessageRequest $request
     * @return bool
     */
    public function replyMessage(ReplyMessageRequest $request): bool;
}
