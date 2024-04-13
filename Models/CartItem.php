<?php

namespace app\Models;

use app\Core\CartModel;
use app\Core\Database;
use app\Core\DBModel;

class CartItem extends DBModel
{
    public string $id = '';
    public string $product_id = '';
    public string $cartId = '';
    public int $quantity = 0;
    public string $note = '';
    public string $name = '';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getTotalPrice()
    {
        $unitPrice = $this->price;
        if ($this->size === 'Medium') {
            $unitPrice += 3000;
        } else if ($this->size === 'Large') {
            $unitPrice += 6000;
        }
        return $unitPrice * $this->quantity;
    }

    public static function tableName(): string
    {
        return 'cart_item';
    }

    public function attributes(): array
    {
        return array_merge($this->defaultAttributes(), ['product_id', 'cart_id', 'quantity', 'note', 'size']);
    }

    public function labels(): array
    {
        return
            [
                'id' => 'Id',
                'product_id' => 'Product Id',
                'cart_id' => 'Cart ID',
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

    public static function getCartItems($cartId)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT 
            cart_item.id, cart_item.product_id, cart_item.cart_id, cart_item.quantity,
            cart_item.note, products.image_url, cart_item.size, products.name,
            products.price, products.description
            FROM cart_item 
            JOIN products 
            ON cart_item.product_id = products.id 
            WHERE cart_item.cart_id = '$cartId';"
        )->fetchAll();

        foreach ($req as $item) {
            $list[] = new CartItem($item);
        }

        return $list;
    }

    public static function getCartItem($itemId)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM cart_item WHERE id = '$itemId'")->fetchAll()[0];
        return new CartItem($req);
    }

    public static function deleteItem($id)
    {
        $sql = "DELETE FROM cart_item WHERE id ='$id'";
        $stmt = self::prepare($sql);
        $stmt->execute();
        return true;
    }
}