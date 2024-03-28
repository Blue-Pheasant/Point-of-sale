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
    public string $created_at = '';
    public string $display = '';

    public function __construct(
        $id,
        $user_id,
        $payment_method,
        $status,
        $delivery_name,
        $delivery_phone,
        $delivery_address,
        $display = '',
        $created_at = ''
    ) 
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->payment_method = $payment_method;
        $this->status = $status;
        $this->delivery_name = $delivery_name;
        $this->delivery_phone = $delivery_phone;
        $this->delivery_address = $delivery_address;
        $this->display = $display;
        if ($created_at != '') {
            $this->created_at = $created_at;
        }
        
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
    public function getDisplay() { return $this->display; }
    public function setDisplay($display) { return $this->display = $display; }


    public static function tableName(): string
    {
        return 'orders';
    }

    public function attributes(): array
    {
        return [
            'id',
            'user_id',
            'payment_method',
            'status',
            'delivery_name',
            'delivery_phone',
            'delivery_address',
            'display',
        ];
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

    public function delete()
    {
        $tablename = $this->tableName();
        $sql = "DELETE FROM $tablename WHERE id=?";
        $stmt= self::prepare($sql);
        $stmt->execute([$this->id]);
        return true;
    }

    public function update(Order $orderModel) 
    {
        $sql = "UPDATE orders SET status='" . $orderModel->status . "' ,
                                  display='" . $orderModel->display . "'
        WHERE id='" . $orderModel->id . "'";
        $statement = self::prepare($sql);
        $statement->execute();
        return true;   
    }

    public static function getTotalPrice()
    {
        $list = [];
        $totalPrice = 0;
        $totalPayment = 0;
        $db = Database::getInstance();
        $req = $db->query(
            "select products.price, order_detail.order_id, orders.id, order_detail.size, order_detail.quantity
            from ((order_detail
            inner join products on order_detail.product_id = products.id)
            inner join orders on order_detail.order_id = orders.id) 
            where orders.status = 'done';"
        );
        
        foreach ($req->fetchAll() as $item) {
            $unitPrice = $item['price'];
            if($item['size'] == 'medium') {
                $unitPrice += 3000;
            } else if($item['size'] == 'large') {
                $unitPrice += 6000;
            }
            $totalPrice += $unitPrice * $item['quantity'];
            $totalPayment += $item['quantity'];
        }    
        array_push($list, $totalPrice, $totalPayment);
        return $list;
    }

    public static function getAllOrders($status)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM orders where status = '$status' ORDER BY status DESC ,created_at DESC");

        foreach ($req->fetchAll() as $item) {
            $list[] = new Order(
                $item['id'],
                $item['user_id'],
                $item['payment_method'],
                $item['status'],
                $item['delivery_name'],
                $item['delivery_phone'],
                $item['delivery_address'],
                $item['display'],
                $item['created_at'],
            );
        };

        return $list;
    }

    public static function getOrders($id)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM orders WHERE user_id = '$id' ORDER BY status DESC ,created_at DESC");

        foreach ($req->fetchAll() as $item) {
            $list[] = new Order(
                $item['id'],
                $item['user_id'],
                $item['payment_method'],
                $item['status'],
                $item['delivery_name'],
                $item['delivery_phone'],
                $item['delivery_address'],
                $item['display'],
                $item['created_at']
            );
        };

        return $list;
    }

    public static function getOrderItem($order_id)
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query(
            "SELECT *
            FROM cart_detail JOIN products ON cart_detail.product_id = products.id 
            WHERE cart_detail.cart_id = '$order_id';"
        );

        foreach ($req->fetchAll() as $item) {
            $list[] = new
                OrderItem(
                    $item['product_id'],
                    $item['cart_id'],
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

    public static function getOrderById($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM orders WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $order = new Order(
            $item['id'],
            $item['user_id'],
            $item['payment_method'],
            $item['status'],
            $item['delivery_name'],
            $item['delivery_phone'],
            $item['delivery_address'],
            $item['display'],
            $item['created_at']
        );
        return $order;
    }
}