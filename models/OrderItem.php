<?php

namespace app\models;

use app\core\Database;
use app\core\DBModel;

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

    public function __construct(
        $id,
        $product_id,
        $order_id,
        $quantity,
        $note,
        $category_id = '',
        $name = '',
        $price = 0,
        $description = '',
        $image_url = '',
        $size = ''
    ) {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->order_id = $order_id;
        $this->quantity = $quantity;
        $this->note = $note;
        $this->category_id = $category_id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->image_url = $image_url;
        $this->size = $size;
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

    public function save()
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
            $list[] = new
                OrderItem(
                    $item['id'],
                    $item['product_id'],
                    $item['order_id'],
                    $item['quantity'],
                    $item['note'],
                    $item['category_id'],
                    $item['name'],
                    $item['price'],
                    $item['description'],
                    $item['image_url'],
                    $item['size']
                );
        }
        return $list;
    }
}