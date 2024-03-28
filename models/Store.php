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

    public function __construct(
        $id  = '',
        $address= '',
        $phone = '',
        $status = '',
        $open_time = '',
        $image_url = ''
    ) {
        $this->id = $id;
        $this->address = $address;
        $this->phone = $phone;
        $this->status = $status;
        $this->open_time = $open_time;
        $this->image_url = $image_url;
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
        return ['id', 'address', 'status', 'image_url', 'open_time', 'phone'];
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
            $list[] = new Store($item['id'], $item['address'], $item['phone'], $item['status'], $item['open_time'], $item['image_url']);
        }

        return $list;
    }

    public function update(Store $store)
    {
        $sql = "UPDATE stores SET   id='$store->id',
                                    status='$store->status', 
                                    address='$store->address',
                                    phone='$store->phone',  
                                    image_url='$store->image_url',
                                    open_time='$store->open_time'
                                    WHERE id='$store->id'";
        $statement = self::prepare($sql);
        $statement->execute();
        return true;  
    }

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM stores WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $store = new Store($item['id'], $item['address'], $item['phone'], $item['status'], $item['open_time'], $item['image_url']);
        return $store;
    }   

    public function delete()
    {
        $tablename = $this->tableName();
        $sql = "DELETE FROM $tablename WHERE id=?";
        $stmt= self::prepare($sql);
        $stmt->execute([$this->id]);
        return true;       
    }
}