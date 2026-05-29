<?php

namespace app\Models;

use app\Core\Database;
use app\Core\DBModel;

class Order extends DBModel
{
    /** Order lifecycle status values (column `orders.status`). */
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_ACCEPTED   = 'accepted';
    public const STATUS_REJECTED   = 'rejected';
    public const STATUS_DONE       = 'done';
    public const STATUS_CANCEL     = 'cancel';

    public string $id = '';
    public string $user_id = '';
    public string $payment_method = '';
    public string $status = '';
    public string $delivery_name = '';
    public string $delivery_phone = '';
    public string $delivery_address = '';
    public string $display = '';
    public string $created_at = '';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getUserId(): string
    {
        return $this->user_id;
    }
    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function getDeliveryName(): string
    {
        return $this->delivery_name;
    }
    public function getDeliveryAddress(): string
    {
        return $this->delivery_address;
    }
    public function getDeliveryPhone(): string
    {
        return $this->delivery_phone;
    }
    public function getDateTime(): string
    {
        return $this->created_at;
    }

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
        return [
            'user_id'          => [self::RULE_REQUIRED],
            'payment_method'   => [self::RULE_REQUIRED],
            'status'           => [self::RULE_REQUIRED],
            'delivery_name'    => [self::RULE_REQUIRED],
            'delivery_phone'   => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'delivery_address' => [self::RULE_REQUIRED],
        ];
    }

    public static function create($user_id, $payment_method, $delivery_name, $delivery_phone, $delivery_address): void
    {
        $order = new Order([
            'user_id'          => $user_id,
            'payment_method'   => $payment_method,
            'status'           => self::STATUS_PROCESSING,
            'delivery_name'    => $delivery_name,
            'delivery_phone'   => $delivery_phone,
            'delivery_address' => $delivery_address,
        ]);
        $order->save();
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

    /**
     * Returns the items belonging to the given order id.
     *
     * @return array<int, OrderItem>
     */
    public static function getOrderItems($order_id): array
    {
        $rows = \app\Common\Query::getAll(
            'SELECT *
            FROM cart_item JOIN products ON cart_item.product_id = products.id
            WHERE cart_item.cart_id = :order_id',
            ['order_id' => $order_id]
        );

        return array_map(fn ($item) => new OrderItem($item), $rows);
    }

    /**
     * @deprecated Use {@see self::getOrderItems()}; this method returns a list,
     *     so the plural name is correct. Kept as a backward-compatible alias.
     *
     * @return array<int, OrderItem>
     */
    public static function getOrderItem($order_id): array
    {
        return self::getOrderItems($order_id);
    }

    public function getDisplay(): string
    {
        return $this->display;
    }
}
