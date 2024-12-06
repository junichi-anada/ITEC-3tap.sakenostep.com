<?php

namespace App\Services\FavoriteItem\Exceptions;

use Exception;

class FavoriteItemException extends Exception
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

    public static function notFound(array $conditions): self
    {
        return new self(
            ['favorite_item' => __('お気に入り商品が見つかりません。')],
            'Favorite item not found with conditions: ' . json_encode($conditions)
        );
    }

    public static function createFailed(array $data): self
    {
        return new self(
            ['data' => __('お気に入り商品の作成に失敗しました。')],
            'Failed to create favorite item: ' . json_encode($data)
        );
    }

    public static function deleteFailed(array $conditions): self
    {
        return new self(
            ['conditions' => __('お気に入り商品の削除に失敗しました。')],
            'Failed to delete favorite item with conditions: ' . json_encode($conditions)
        );
    }

    public static function restoreFailed(int $id): self
    {
        return new self(
            ['id' => __('お気に入り商品の復元に失敗しました。')],
            "Failed to restore favorite item with ID: {$id}"
        );
    }

    public static function listFailed(array $conditions): self
    {
        return new self(
            ['conditions' => __('お気に入り商品一覧の取得に失敗しました。')],
            'Failed to get favorite items with conditions: ' . json_encode($conditions)
        );
    }
}
