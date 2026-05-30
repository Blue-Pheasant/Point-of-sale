<?php

namespace app\Exception;

/**
 * Thrown when an order cannot be placed because one or more items do not have
 * enough stock on hand (roadmap T20). Carries the offending product id so the
 * caller can surface a useful message.
 */
class OutOfStockException extends \RuntimeException
{
    protected $message = 'Sản phẩm đã hết hàng.';
    protected $code    = 409;

    private string $productId;

    public function __construct(string $productId, string $message = '')
    {
        $this->productId = $productId;
        parent::__construct($message !== '' ? $message : $this->message, $this->code);
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
