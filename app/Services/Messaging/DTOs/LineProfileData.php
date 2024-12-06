<?php

namespace App\Services\Messaging\DTOs;

class LineProfileData
{
    public function __construct(
        public readonly string $userId,
        public readonly string $displayName,
        public readonly ?string $pictureUrl = null,
        public readonly ?string $statusMessage = null,
        public readonly ?string $language = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['userId'],
            displayName: $data['displayName'],
            pictureUrl: $data['pictureUrl'] ?? null,
            statusMessage: $data['statusMessage'] ?? null,
            language: $data['language'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'userId' => $this->userId,
            'displayName' => $this->displayName,
            'pictureUrl' => $this->pictureUrl,
            'statusMessage' => $this->statusMessage,
            'language' => $this->language,
        ], fn($value) => !is_null($value));
    }

    /**
     * LINE APIのレスポンスからDTOを作成
     *
     * @param array $response LINE APIのレスポンス
     * @return self
     */
    public static function fromLineResponse(array $response): self
    {
        return self::fromArray($response);
    }

    /**
     * AuthenticateOauthモデル用のデータを取得
     *
     * @param int $providerId LINEのプロバイダーID
     * @param int $siteId サイトID
     * @return array
     */
    public function toAuthenticateOauthData(int $providerId, int $siteId): array
    {
        return [
            'auth_provider_id' => $providerId,
            'auth_code' => $this->userId,
            'token' => $this->userId,
            'site_id' => $siteId,
            'entity_type' => 'user',
            'entity_id' => null,
            'expires_at' => null,
        ];
    }
}
