<?php

namespace app\models;

use app\core\CartModel;
use app\core\Controller;
use app\core\Database;
use app\core\DBModel;

class OrderDetail extends DBModel
{
    public string $id = '';
    public string $product_id = '';
    public string $order_id = '';
    public string $quantity = '';
    public string $note = '';
    public string $size = '';

    public function __construct(
        $id = '',
        $product_id = '',
        $order_id = '',
        $quantity = '',
        $note = '',
        $size = ''
    ) {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->order_id = $order_id;
        $this->quantity = $quantity;
        $this->note = $note;
        $this->size = $size;
    }

    public static function tableName(): string
    {
        return 'order_detail';
    }

    public function attributes(): array
    {
        return ['id', 'product_id', 'order_id', 'quantity', 'note', 'size'];
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

    public function save()
    {
        return parent::save();
    }
}