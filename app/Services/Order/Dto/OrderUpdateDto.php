<?php

namespace App\Services\Order\Dto;

class OrderUpdateDto
{
    /**
     * @var string
     */
    private $orderCode;

    /**
     * @var array
     */
    private $details;

    /**
     * コンストラクタ
     *
     * @param string $orderCode
     * @param array $details [['item_code' => string, 'quantity' => int], ...]
     */
    public function __construct(string $orderCode, array $details)
    {
        $this->orderCode = $orderCode;
        $this->details = $details;
    }

    /**
     * 注文コードを取得
     *
     * @return string
     */
    public function getOrderCode(): string
    {
        return $this->orderCode;
    }

    /**
     * 注文明細を取得
     *
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * リクエストデータからDTOを作成
     *
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['order_code'],
            $data['details']
        );
    }
}
