<?php

namespace app\models;

use app\core\CategoryModel;

class Category extends CategoryModel
{
    public string $id;
    public string $category_name;
    public string $create_at;
    
    public function __construct()
    {
        $this->id = '';
        $this->category_name = '';
        $this->create_at = '';
        parent::__construct();
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
        return parent::save();
    }
}