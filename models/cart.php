<?php

namespace app\models;

use app\core\CartModel;

class Cart extends CartModel 
{
    public array $list;
    public int $status;
    
    public function __construct(
        $status = 0,
        $list = []
    ) {
        $this->status = $status;
        $this->list = $list;
    }
    
    public static function tableName(): string
    {
        return 'carts';
    }

    public function attributes(): array
    {
        return ['list'];
    }

    public function labels(): array
    {
        return [
            'list' => 'List',
            'status' => 'Status',
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public function save()
    {
        return parent::save();
    }

    public function getDisplayInfo(): string
    {
        return $this->list . ' ' . $this->status;
    }

}