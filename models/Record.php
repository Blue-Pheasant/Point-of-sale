<?php

namespace app\models;

use app\core\RecordModel;
use app\models\Product;
class Record extends RecordModel 
{
    private $id;
    public function getId () { return $this->id; }
    private function setId ($id) { $this->id = $id; }

    private $userID;
    public function getUserID () { return $this->userID; }
    private function setUserID ($userID) { $this->userID = $userID; }

    private $productID;
    public function getProductID() { return $this->productID; }
    private function setProductID ($productID) { $this->productID = $productID; }

    private $quantity;
    public function getQuantity() { return $this->quantity; }
    private function setQuantity($quantity) { $this->quantity = $quantity; }

    private $totalPrice;
    public function getTotalPrice() { return $this->totalPrice; }
    private function setTotalPrice($totalPrice) { $this->totalPrice = $totalPrice; }

    private $create_at;
    public function getSaleDate () { return $this->create_at; }
    private function setSaleDate ($create_at) { $this->create_at = $create_at; }
    
    public function __construct(
        $userID,
        $productID,
        $quantity,
        $totalPrice,
        $create_at = '',
        $id = null
    ) {
        $this->userID = $userID;
        $this->productID = $productID;
        $this->quantity = $quantity;
        $this->create_at = $create_at;
        $this->id = $id;
    }
    
    public static function tableName(): string
    {
        return 'records';
    }

    public function attributes(): array
    {
        return ['ID', 'USERID', 'PRODUCTID', 'QUANTITY', 'SALEDATE'];
    }

    public function labels(): array
    {
        return [
            'SALEDATE' => 'Sale Date',
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public function save()
    {
        $productModel = Product::get($this->productID);
        $this->totalPrice = $productModel->getPrice() * $this->quantity;
        $this->create_at = date("Y-m-d" . " H:i:s",time() + 7 * 3600);
        return parent::save();
    }

    public function getDisplayInfo(): string
    {
        return $this->userID . ' ' . $this->create_at;
    }

    public function create()
    {

    }

    public function edit()
    {

    }

    public function delete()
    {

    }

    public static function get($id)
    {
        $model = null;
        return $model;
    }

    public static function getAll()
    {
        $models = [];
        return $models;
    }
}