<?php

namespace App\Models;

use App\Core\Application;
use App\Core\CartModel;
use App\Core\Database;
use App\Core\DBModel;

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
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM cart WHERE user_id = '$id' AND status = 'processing'");


        foreach ($req->fetchAll() as $item) {
            $list[] = new Cart($item);
        };

        return $list;
    }
}