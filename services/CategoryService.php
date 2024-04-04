<?php

namespace app\services;

use app\models\Category;
use app\core\Database;
use PDO;

class CategoryService
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllCategories() : array
    {
        $req = $this->db->query("SELECT * FROM categories")->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new Category($item);
        }

        return $list;
    }

    public function getCategoryById($id): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new Category($result) : null;
    }
}