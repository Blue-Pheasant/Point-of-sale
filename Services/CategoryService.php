<?php

namespace app\Services;

use app\Models\Category;
use PDO;

class CategoryService
{
    public function __construct(private PDO $db)
    {
    }

    public function getAllCategories(): array
    {
        $req = $this->db->query('SELECT * FROM categories WHERE deleted_at IS NULL')->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new Category($item);
        }

        return $list;
    }

    public function getCategoryById($id): ?Category
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new Category($result) : null;
    }
}
