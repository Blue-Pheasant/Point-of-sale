<?php

namespace app\models;

use app\core\Application;
use app\core\CartModel;
use app\core\Database;
use app\core\DBModel;

class Order extends DBModel
{
    public string $id = '';
    public string $user_id = '';
    public string $payment_method = '';
    public string $status = '';
    public string $delivery_name = '';
    public string $delivery_phone = '';
    public string $delivery_address = '';
    public $display = '';
    public string $created_at = '';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getId () { return $this->id; }
    public function getUserId () { return $this->user_id; }
    public function getPaymentMethod() { return $this->payment_method; }
    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
    public function getDeliveryName() { return $this->delivery_name; }
    public function getDeliveryAddress() { return $this->delivery_address; }
    public function getDeliveryPhone() { return $this->delivery_phone; }
    public function getDateTime() { return $this->created_at; }

    public static function tableName(): string
    {
        return 'orders';
    }

    public function attributes(): array
    {
        $attributes = ['user_id', 'payment_method', 'status', 'delivery_name', 'delivery_phone', 'delivery_address', 'display'];
        return array_merge($this->defaultAttributes(), $attributes);
    }

    public function labels(): array
    {
        return
            [
                'id' => 'ID',
                'user_id' => 'User ID',
                'payment_method' => 'Payment method',
                'status' => 'Status',
                'delivery_name' => 'Delivery name',
                'delivery_phone' => 'Delivery phone',
                'delivery_address' => 'Delivery address',
            ];
    }

    public function rules(): array
    {
        return [];
    }

    public static function create($user_id, $payment_method, $delivery_name, $delivery_phone, $delivery_address)
    {
        $order = new Order(uniqid(), $user_id, $payment_method, 'processing', $delivery_name, $delivery_phone, $delivery_address);
        $order->save();
    }

    public function save()
    {
        return parent::save();
    }

    public static function getAllOrders($status)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM orders where status = '$status' ORDER BY status DESC ,created_at DESC");

        foreach ($req->fetchAll() as $item) {
            $list[] = new Order($item);
        };

        return $list;
    }

    public static function getOrders($id)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM orders WHERE user_id = '$id' ORDER BY status DESC ,created_at DESC");

        foreach ($req->fetchAll() as $item) {
            $list[] = new Order($item);
        };

        return $list;
    }

    public static function getOrderItem($order_id)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query(
            "SELECT *
            FROM cart_item JOIN products ON cart_item.product_id = products.id 
            WHERE cart_item.cart_id = '$order_id';"
        );

        foreach ($req->fetchAll() as $item) {
            $list[] = new OrderItem($item);
        }
        return $list;
    }

    public function getDisplay() : string
    {
        return $this->display;
    }
}