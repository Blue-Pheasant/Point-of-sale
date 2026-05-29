<?php

namespace app\Models;

use app\Core\Database;
use app\Core\DBModel;

class Product extends DBModel
{
    public string $category_id;
    public string $name;
    public float $price;
    public string $description;
    public string $image_url;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
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
    public function getName()
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

    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }
    public function getImageUrl()
    {
        return $this->image_url;
    }

    public static function getNameById($id): string
    {
        $productModel = Product::getProductDetail($id);
        return $productModel?->getName() ?? '';
    }

    public function getCategory()
    {
        $categoryModel = Category::get($this->category_id);
        return $categoryModel->getDisplayName();
    }

    public function getDisplayInfo(): string
    {
        return $this->id . ' ' . $this->category_id . ' ' . $this->name . ' ' . $this->price . ' ' . $this->description;
    }

    public static function tableName(): string
    {
        return 'products';
    }

    public function attributes(): array
    {
        return array_merge($this->defaultAttributes(), ['category_id', 'name', 'price', 'description', 'image_url']);
    }

    public function labels(): array
    {
        return [
            'id' => 'Mã sản phẩm',
            'name' => 'Tên sản phẩm',
            'price' => 'Giá',
            'description' => 'Mô tả sản phẩm',
            'image_url' => 'Hình ảnh sản phẩm',
            'category_id' => 'Mã mục',
        ];
    }

    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute];
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'description' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 20], [self::RULE_MAX, 'max' => 100]],
            'price' => [self::RULE_REQUIRED],
        ];
    }

    public function save(): bool
    {
        $this->id = uniqid();
        return parent::save();
    }

    public static function getAllProducts()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM products');

        foreach ($req->fetchAll() as $item) {
            $list[] = new Product($item);
        }

        return $list;
    }

    public static function getProductDetail($id): ?Product
    {
        $row = \app\Common\QueryBuilder::table('products')
            ->where('id', $id)
            ->first();

        return $row ? new Product($row) : null;
    }

    public static function getProductsByCategory($category_id)
    {
        $list = [];
        $rows = \app\Common\QueryBuilder::table('products')
            ->where('category_id', $category_id)
            ->get();

        foreach ($rows as $item) {
            $list[] = new Product($item);
        }

        return $list;
    }

    public static function getProductsByKeyword($keyword)
    {
        $list = [];
        $rows = \app\Common\QueryBuilder::table('products')
            ->whereLike('name', (string) $keyword)
            ->get();

        foreach ($rows as $item) {
            $list[] = new Product($item);
        }

        return $list;
    }
}
