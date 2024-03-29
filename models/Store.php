<?php

namespace app\models;

use app\core\Database;
use app\core\DBModel;


class Store extends DBModel
{
    public string $id;
    public string $address;
    public string $status;
    public string $phone;
    public string $open_time;
    public string $image_url;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getId () { return $this->id; }
    private function setId ($id) { $this->id = $id; }

    public function getAddress() { return $this->address; }
    private function setAddress($address) { $this->address = $address; }

    public function getHotline() { return $this->phone; }
    private function setHotline($phone) { $this->phone = $phone; }
    
    public function getStatus() { return $this->status; }
    private function setStatus($status) { $this->status = $status; }

    public function getOpentime() { return $this->open_time; }
    private function setOpentime($open_time) { $this->open_time = $open_time; }

    public static function tableName(): string
    {
        return 'stores';
    }

    public function attributes(): array
    {
        return array_merge($this->defaultAttributes(), ['address', 'status', 'image_url', 'open_time', 'phone']);
    }

    public function labels(): array
    {
        return [
            'open_time' => 'Giờ mở cửa',
            'address' => 'Địa chỉ',
            'phone' => 'Số điện thoại',
            'description' => 'Giới thiệu về cửa hàng',
            'status' => 'Tình trạng cửa hàng',
            'image_url' => 'Hình ảnh cửa hàng'
        ];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute];
    }
        
    public function rules(): array
    {
        return [
            'status' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' <= 13]],
            'address' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' <= 1000]]
        ];
    }

    public function save()
    {
        $this->id = uniqid();
        return parent::save();
    }

    public function getDisplayInfo(): string
    {
        return $this->name . ' ' . $this->address . ' ' . $this->phone;
    }

    public static function getAll()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM stores');

        foreach ($req->fetchAll() as $item) {
            $list[] = new Store($item);
        }

        return $list;
    }

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM stores WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $store = new Store($item);
        return $store;
    }
}