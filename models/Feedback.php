<?php

namespace app\models;

use app\core\Database;
use app\core\FeedbackModel;
use PDO;

class Feedback extends FeedbackModel
{
    public string $id;
    public string $customer_id;
    public string $product_id;
    public float $start;
    public string $comment;
    public string $create_at;
    
    public function __construct(
        $customer_id = '',
        $product_id = '',
        $start = 0,
        $comment = ''
    ) {
        $this->customer_id = $customer_id;
        $this->product_id = $product_id;
        $this->starts = $start;
        $this->comment = $comment;
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
        return ['id', 'product_id', 'customer_id', 'start', 'comment'];
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

    public static function getAll()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM feedbacks');

        foreach ($req->fetchAll() as $item) {
            $list[] = new Feedback($item['customer_id'], $item['product_id'], $item['start'], $item['comment']);
        }
        return $list;
    }

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM feedbacks WHERE id = "' . $id . '"');
        $item = $req->fetchAll()[0];
        $feedback = new Feedback($item['customer_id'], $item['product_id'], $item['start'], $item['comment']);
        return $feedback;
    }  
}