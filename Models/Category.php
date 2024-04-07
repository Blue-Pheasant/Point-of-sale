<?php

namespace app\Models;

use app\Core\Database;
use app\Core\DBModel;

class Category extends DBModel
{
    public string $id;
    public string $name;
    
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
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

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM categories WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $categories = new Category($item);
        return $categories;
    } 
}