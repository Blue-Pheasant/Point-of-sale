<?php

namespace app\models;

use app\core\Application;
use app\core\Database;
use app\core\Model;
use app\core\ProductModel;
use app\core\Request;
use PDO;

class Product extends ProductModel
{
    public string $id;
    public string $category_id;
    public string $name;
    public float $price;
    public string $description;
    public string $image_url;
    public string $create_at;
    public string $update_at;

    public function __construct(
        $category_id = '',
        $name = '',
        $price = 0,
        $description = '',
        $create_at = '',
        $quantity = 1
    ) {
        $this->category_id = $category_id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }
    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    public function getname()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function getPrice()
    {
        return $this->price;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getDescription()
    {
        return $this->description;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getDisplayInfo(): string
    {
        return $this->id . ' ' . $this->category_id . ' ' . $this->name . ' ' . $this->quantity . ' ' . $this->price . ' ' . $this->description . ' ' . $this->create_at;
    }

    public static function tableName(): string
    {
        return 'products';
    }

    public function attributes(): array
    {
        return ['id', 'category_id', 'product_id', 'quantity', 'price'];
    }

    public function labels(): array
    {
        return [
            'name' => 'Product name',
            'price' => 'Price',
            'quantity' => 'Quanity',
            'description' => 'Description',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MIN, 'max' <= 50]],
            'description' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' >= 20], [self::RULE_MAX, 'max' <= 100]],
            'price' => [self::RULE_REQUIRED],
            'quantity' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' >= 1]]
        ];
    }

    public function save()
    {
        $this->id = uniqid();
        return parent::save();
    }

    public function update()
    {

    }

    public function delete()
    {
        $tablename = $this->tableName();
        $id = $this->id;
        $sql = "DELETE FROM $tablename WHEHRE ID = :ID";
        $statement = self::prepare($sql);
        $statement->bindParam(':ID', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function getAll()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM products');

        foreach ($req->fetchAll() as $item) {
            $list[] = new Product($item['id'], $item['category_id'], $item['name'], $item['price'], $item['description'], $item['image_url']);
        }

        return $list;
    }

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM products WHERE id = "' . $id . '"');
        $item = $req->fetchAll()[0];
        $product = new Product($item['id'], $item['category_id'], $item['name'], $item['price'], $item['description'], $item['image_url']);
        return $product;
    }
}