<?php

namespace App\Services\ItemCategory\Exceptions;

use Exception;

class CategoryException extends Exception
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

    public static function notFound(int $id): self
    {
        return new self(
            ['id' => __('カテゴリが見つかりません。')],
            "Category not found with ID: {$id}"
        );
    }

    public static function createFailed(array $data): self
    {
        return new self(
            ['data' => __('カテゴリの作成に失敗しました。')],
            'Failed to create category: ' . json_encode($data)
        );
    }

    public static function updateFailed(int $id): self
    {
        return new self(
            ['id' => __('カテゴリの更新に失敗しました。')],
            "Failed to update category with ID: {$id}"
        );
    }

    public static function deleteFailed(int $id): self
    {
        return new self(
            ['id' => __('カテゴリの削除に失敗しました。')],
            "Failed to delete category with ID: {$id}"
        );
    }

    public static function invalidCategoryCode(string $categoryCode): self
    {
        return new self(
            ['category_code' => __('無効なカテゴリコードです。')],
            "Invalid category code: {$categoryCode}"
        );
    }

    public static function breadcrumbsFailed(int $categoryId): self
    {
        return new self(
            ['category_id' => __('パンくずリストの取得に失敗しました。')],
            "Failed to get breadcrumbs for category ID: {$categoryId}"
        );
    }
}
