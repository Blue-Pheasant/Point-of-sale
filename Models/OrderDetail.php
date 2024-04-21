<?php

namespace app\Models;

use app\Core\CartModel;
use app\Core\Controller;
use app\Core\Database;
use app\Core\DBModel;

class OrderDetail extends DBModel
{
    public string $id = '';
    public string $product_id = '';
    public string $order_id = '';
    public string $quantity = '';
    public string $note = '';
    public string $size = '';

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
        return array_merge($this->defaultAttributes(), ['product_id', 'order_id', 'quantity', 'note', 'size']);
    }

    public function labels(): array
    {
        return
            [
                'id' => 'ID',
                'product_id' => 'Product ID',
                'order_id' => 'Order ID',
                'quantity' => 'Quantity',
                'note' => 'Note',
                'size' => 'Size'
            ];
    }

    public function rules(): array
    {
        return [];
    }

    public function save(): bool
    {
        return parent::save();
    }
}