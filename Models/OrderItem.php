<?php

namespace app\Models;

use app\Core\DBModel;

class OrderItem extends DBModel
{
    public string $id = '';
    public string $product_id = '';
    public string $order_id = '';
    public int $quantity = 0;
    public string $note = '';
    public string $category_id = '';
    public string $name = '';
    public float $price = 0;
    public string $description = '';
    public string $image_url = '';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function tableName(): string
    {
        return 'order_detail';
    }

    public function attributes(): array
    {
        return ['id', 'product_id', 'order_id', 'quantity', 'note', 'category_id', 'name', 'price', 'description', 'image_url', 'size'];
    }

    public function labels(): array
    {
        return
            [
                'id' => 'ID',
                'product_id' => 'Product ID',
                'order_id' => 'Cart ID',
                'quantity' => 'Quantity',
                'note' => 'Note',
                'name' => 'Product name',
                'price' => 'Price',
                'description' => 'Description',
                'size' => 'Size',
            ];
    }

    public function rules(): array
    {
        return [
            'product_id' => [self::RULE_REQUIRED],
            'order_id'   => [self::RULE_REQUIRED],
            'quantity'   => [self::RULE_REQUIRED, [self::RULE_MIN_VALUE, 'minint' => 1]],
        ];
    }

    public function getDisplayInfo(): string
    {
        return $this->list . ' ' . $this->status;
    }

    /**
     * Returns the order-detail items belonging to the given order id.
     *
     * @return array<int, OrderItem>
     */
    public static function getOrderItems($order_id): array
    {
        $rows = \app\Common\Query::getAll(
            'SELECT *
            FROM order_detail JOIN products ON order_detail.product_id = products.id
            WHERE order_detail.order_id = :order_id',
            ['order_id' => $order_id]
        );

        return array_map(fn ($item) => new OrderItem($item), $rows);
    }

    /**
     * @deprecated Use {@see self::getOrderItems()}; this method returns a list,
     *     so the plural name is correct. Kept as a backward-compatible alias.
     *
     * @return array<int, OrderItem>
     */
    public static function getOrderItem($order_id): array
    {
        return self::getOrderItems($order_id);
    }
}
