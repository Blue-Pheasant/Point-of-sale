<?php

namespace app\models;

use app\core\FeedbackModel;

class Feedback extends FeedbackModel
{
    public string $id;
    public string $customer_id;
    public string $product_id;
    public float $start;
    public string $comment;
    public string $create_at;
    
    public function __construct()
    {
        $this->id = '';
        $this->customer_id = '';
        $this->product_id = '';
        $this->start = 0;
        $this->comment = '';
        $this->create_at = '';
        parent::__construct();
    }

    public function getDisplayInfo(): string
    {
        return $this->product_id . ' ' . $this->customer_id . ' ' . $this->comment . ' ' . $this->start . ' ' . $this->create_at;
    }

    public static function tableName(): string
    {
        return 'feedbacks';
    }

    public function attributes(): array
    {
        return ['id', 'product_id', 'customer_id', 'start', 'comment', 'create_at'];
    }

    public function labels(): array
    {
        return [
            'product_id' => 'Product id',
            'customer_id' => 'Customer id',
            'comment' => 'Comment',
            'start' => 'Average rating',
            'create_at' => 'Created at',
        ];
    }

    public function rules(): array
    {
        return [
            'comment' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 10]],
            'start' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' >= 1], [self::RULE_MAX, 'max' <= 5]],
        ];
    }

    public function save()
    {
        return parent::save();
    }
}