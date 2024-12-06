<?php

namespace App\Services\Item\Exceptions;

use Exception;

class ItemException extends Exception
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

    public static function notFound(string $itemCode): self
    {
        return new self(
            ['item_code' => __('商品が見つかりません。')],
            "Item not found with code: {$itemCode}"
        );
    }

    public static function createFailed(array $data): self
    {
        return new self(
            ['data' => __('商品の作成に失敗しました。')],
            'Failed to create item: ' . json_encode($data)
        );
    }

    public static function updateFailed(string $itemCode): self
    {
        return new self(
            ['item_code' => __('商品の更新に失敗しました。')],
            "Failed to update item with code: {$itemCode}"
        );
    }

    public static function deleteFailed(string $itemCode): self
    {
        return new self(
            ['item_code' => __('商品の削除に失敗しました。')],
            "Failed to delete item with code: {$itemCode}"
        );
    }

    public static function searchFailed(string $reason): self
    {
        return new self(
            ['search' => __('商品の検索に失敗しました。')],
            "Search failed: {$reason}"
        );
    }
}
