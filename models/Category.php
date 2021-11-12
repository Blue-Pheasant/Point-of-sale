<?php

namespace app\models;

use app\core\CategoryModel;
use app\core\Database;
use PDO;

class Category extends CategoryModel
{
    public string $id;
    public string $category_name;
    public string $create_at;
    
    public function __construct(
        $category_name = ''
    ) {
        $this->category_name = $category_name;
    }

    public function getDisplayName(): string
    {
        return $this->category_name . ' ' . $this->create_at;
    }

    public static function tableName(): string
    {
        return 'categories';
    }

    public function attributes(): array
    {
        return ['id', 'name', 'create_at'];
    }

    public function labels(): array
    {
        return [
            'name' => 'Name',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' <= 30]],
        ];
    }

    public function save()
    {
        $this->id = uniqid();
        return parent::save();
    }

    public function create()
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

    public function update()
    {
        
    }

    public static function getAll()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM categories');

        foreach ($req->fetchAll() as $item) {
            $list[] = new Category($item['id'], $item['name']);
        }

        return $list;
    }

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM categories WHERE id = "' . $id . '"');
        $item = $req->fetchAll()[0];
        $product = new Category($item['id'], $item['name']);
        return $product;
    }
}