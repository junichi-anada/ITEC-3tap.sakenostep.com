<?php

namespace App\Services\Auth\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected array $errors;

    public function __construct(array $errors, string $message = '', int $code = 0, ?Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function invalidSite(string $siteCode): self
    {
        return new self(
            ['site_code' => __('無効なサイトコードです。')],
            "Invalid site code: {$siteCode}"
        );
    }

    public static function invalidCredentials(): self
    {
        return new self(
            ['login_code' => __('電話番号またはお客様番号に誤りがあります。')],
            'Invalid credentials provided'
        );
    }
}
