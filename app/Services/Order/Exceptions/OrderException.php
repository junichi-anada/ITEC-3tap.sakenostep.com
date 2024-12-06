<?php

declare(strict_types=1);

namespace App\Services\Order\Exceptions;

use Exception;

/**
 * 注文関連の例外クラス
 */
class OrderException extends Exception
{
    /**
     * @param string $message エラーメッセージ
     * @param int $code エラーコード
     * @param \Throwable|null $previous 前の例外
     */
    public function __construct(
        string $message = '注文処理中にエラーが発生しました。',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * 注文が見つからない場合の例外を作成
     */
    public static function notFound(int $id): self
    {
        return new self("ID: {$id} の注文が見つかりません。");
    }

    /**
     * 注文の更新が許可されていない場合の例外を作成
     */
    public static function updateNotAllowed(): self
    {
        return new self('この注文は更新できません。');
    }

    /**
     * 注文の削除が許可されていない場合の例外を作成
     */
    public static function deleteNotAllowed(): self
    {
        return new self('この注文は削除できません。');
    }

    /**
     * 注文の作成に失敗した場合の例外を作成
     */
    public static function creationFailed(string $reason): self
    {
        return new self("注文の作成に失敗しました: {$reason}");
    }

    /**
     * 注文の更新に失敗した場合の例外を作成
     */
    public static function updateFailed(string $reason): self
    {
        return new self("注文の更新に失敗しました: {$reason}");
    }

    /**
     * 注文の削除に失敗した場合の例外を作成
     */
    public static function deleteFailed(string $reason): self
    {
        return new self("注文の削除に失敗しました: {$reason}");
    }
}
