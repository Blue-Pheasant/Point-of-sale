<?php

namespace app\Models;

use app\Core\Application;
use app\Core\CartModel;
use app\Core\Database;
use app\Core\DBModel;

class Cart extends DBModel
{
    public string $id = '';
    public string $user_id = '';
    public string $status = '';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function tableName(): string
    {
        return 'cart';
    }

    public function attributes(): array
    {
        return ['id', 'user_id', 'status'];
    }

    public function labels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'user ID',
            'status' => 'Status',
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public static function create($id)
    {
        $cart = new Cart(['id' => uniqid(), 'user_id' => $id, 'status' => 'processing']);
        $cart->save();
    }

    public static function checkoutCart($id)
    {
        $db = Database::getInstance();
        $db->query("UPDATE cart SET status = 'done' WHERE id = '$id'");
    }

    public function save()
    {
        return parent::save();
    }

    public static function findCart($id)
    {
        $cart = Cart::getCart($id);
        if (count($cart) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getDisplayInfo(): string
    {
        return $this->records . ' ' . $this->status;
    }


    public static function getCart($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM cart WHERE user_id = '$id' AND status = 'processing'");

        return new Cart($req->fetchAll()[0]);
    }
}