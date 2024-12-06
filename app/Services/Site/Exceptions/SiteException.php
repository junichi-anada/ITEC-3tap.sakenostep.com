<?php

namespace App\Services\Site\Exceptions;

use Exception;

class SiteException extends Exception
{
    protected array $errors;

    public function __construct(array $errors = [], string $message = '', int $code = 0, ?Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function notFound(string $identifier): self
    {
        return new self(
            ['site' => __('サイトが見つかりません。')],
            "Site not found with identifier: {$identifier}"
        );
    }

    public static function createFailed(array $data): self
    {
        return new self(
            ['data' => __('サイトの作成に失敗しました。')],
            'Failed to create site: ' . json_encode($data)
        );
    }

    public static function updateFailed(int $id): self
    {
        return new self(
            ['id' => __('サイトの更新に失敗しました。')],
            "Failed to update site with ID: {$id}"
        );
    }

    public static function deleteFailed(int $id): self
    {
        return new self(
            ['id' => __('サイトの削除に失敗しました。')],
            "Failed to delete site with ID: {$id}"
        );
    }

    public static function invalidSiteCode(string $siteCode): self
    {
        return new self(
            ['site_code' => __('無効なサイトコードです。')],
            "Invalid site code: {$siteCode}"
        );
    }
}
