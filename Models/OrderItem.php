<?php

namespace app\Models;

use app\Core\Database;
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
        return [];
    }

    public function save(): bool
    {
        return parent::save();
    }

    public function getDisplayInfo(): string
    {
        return $this->list . ' ' . $this->status;
    }

    public static function getOrderItem($order_id)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query(
            "SELECT *
            FROM order_detail JOIN products ON order_detail.product_id = products.id 
            WHERE order_detail.order_id = '$order_id';"
        );

        foreach ($req->fetchAll() as $item) {
            $list[] = new OrderItem($item);
        }
        return $list;
    }
}