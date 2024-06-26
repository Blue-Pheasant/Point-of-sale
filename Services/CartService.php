<?php

namespace app\Services;

use app\Core\Database;
use app\Models\Cart;
use app\Models\CartItem;
use app\Common\Pagination;
use app\Common\Query;
use PDO;

class CartService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getCartByUserId($userId): ?Cart
    {
        $stmt = $this->db->prepare("SELECT * FROM cart WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new Cart($result) : null;
    }

    public function getCartItems($cartId)
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

    public function getCartItem($cartItemId)
    {
        $req = $this->db->query("SELECT * FROM cart_item WHERE id = '$cartItemId'")->fetchAll()[0];
        return new CartItem($req);
    }

    public function checkOutCart($cartId)
    {
        $this->db->query("UPDATE cart SET status = 'done' WHERE id = '$cartId'");
    }

    public function getCartIdFromUserId($userId)
    {
        $req = $this->db->query("SELECT id FROM cart WHERE user_id = '$userId'")->fetchAll();
        return $req[0]['id'] ?? null;
    }
}