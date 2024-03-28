<?php

namespace app\models;

use app\core\Database;
use app\core\DBModel;

class Category extends DBModel
{
    public string $id;
    public string $name;
    
    public function __construct(
        $id = '',
        $name = ''
    ) {
        $this->name = $name;
        $this->id = $id;
    }

    public function getName() 
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute];
    }
    
    public static function tableName(): string
    {
        return 'categories';
    }

    public function attributes(): array
    {
        return ['id', 'name'];
    }

    
    public function labels(): array
    {
        return [
            'name' => 'Tên mục',
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

    public function delete()
    {
        $tablename = $this->tableName();
        $sql = "DELETE FROM $tablename WHERE id=?";
        $stmt= self::prepare($sql);
        $stmt->execute([$this->id]);
        return true;
    }

    public function update($category)
    {
        $sql = "UPDATE categories SET name='$category->name' 
                                    WHERE id='$category->id'";
        $statement = self::prepare($sql);
        $statement->execute();
        return true;         
    }

    public static function getAllCategories()
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
        $req = $db->query("SELECT * FROM categories WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $categories = new Category($item['id'], $item['name']);
        return $categories;
    } 
}