<?php

namespace app\models;

use app\core\Application;
use app\core\ProductModel;
use app\core\Request;

class Product extends ProductModel
{
    private string $id;
    private string $category_id;
    private string $name;
    private float $price;
    private int $quantity;
    private string $description;
    private string $create_at;
    private string $update_at;

    public function __construct(
        $id = '',
        $category_id = '',
        $name = '',
        $price = 0,
        $description = '',
        $create_at = ''
    ) {
        $this->id = $id;
        $this->category_id = $category_id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }
    
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }

    public function setCategoryId($category_id) { $this->category_id = $category_id; }
    public function getCategoryId() { return $this->category_id; }

    public function setName($name) { $this->name = $name; }
    public function getname() { return $this->name; }

    public function setPrice($price) { $this->price = $price; }
    public function getPrice() { return $this->price; }

    public function setDescription($description) { $this->description = $description; }
    public function getDescription() { return $this->description; }

    public function setQuantity($quantity) { $this->quantity = $quantity; }
    public function getQuantity() { return $this->quantity; }

    public function getDisplayInfo(): string
    {
        return $this->id . ' ' . $this->category_id . ' ' . $this->name . ' ' . $this->quantity . ' ' . $this->price . ' ' . $this->description . ' ' . $this->create_at;
    }

    public static function tableName(): string
    {
        return 'feedbacks';
    }

    public function attributes(): array
    {
        return ['id', 'product_id', 'customer_id', 'price', 'comment', 'create_at'];
    }

    public function labels(): array
    {
        return [
            'name' => 'Product name',
            'price' => 'Price',
            'description' => 'Description',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MIN, 'max' <= 50]],
            'description' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' >= 20], [self::RULE_MAX, 'max' <= 100]],
            'price' => [self::RULE_REQUIRED],
        ];
    }

    public function save()
    {
        $this->create_at = date("Y-m-d" . " H:i:s",time() + 7 * 3600);
        $this->id = uniqid();
        return parent::save();
    }

    public function update()
    {
        $this->update_at = date("Y-m-d" . " H:i:s",time() + 7 * 3600);
        return parent::update();
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

    public static function getAll()
    {
        $models = [];
        return $models;
    }
}