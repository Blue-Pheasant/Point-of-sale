<?php

namespace app\services;

use app\core\Database;
use app\models\Product;
use app\Common\Pagination;
use app\Common\Query;
use PDO;

class ProductService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllProducts($pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
        
        $totalCount = Query::getCount("SELECT * FROM products");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        
        $offset = $pagination['offset'];
        $query = Query::get('products', [], [], $limit, $offset);
        
        $req = $this->db->query($query)->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new Product($item);
        }

        $result = [
            'list' => $list,
            'pagination' => $pagination
        ];

        return $result;
    }

    public function getProductById($id): ?Product
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new Product($result) : null;
    }

    public function getProductCategory($categoryId, $pagerCondition = []): array
    {
        $limit = $pagerCondition['limit'] ?? 10;
        $page = $pagerCondition['page'] ?? 1;

        $query = "SELECT * FROM products WHERE category_id = :categoryId";
        $params = ['categoryId' => $categoryId];

        // Get total count for pagination
        $totalCount = Query::getCount("SELECT * FROM products WHERE category_id = :categoryId", $params);
        $pagination = Pagination::paginate($limit, $page, $totalCount);

        // Fetch products with pagination
        $offset = $pagination['offset'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params = array_merge($params, ['limit' => $limit, 'offset' => $offset]);
        $products = Query::getAll($query, $params);

        $list = array_map(function ($item) {
            return new Product($item);
        }, $products);

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }

    public function createProduct(array $data): bool
    {
        $db = $this->db->beginTransaction();

        try {
            $product = new Product($data);
            $result = $product->save();

            if ($result) {
                $db->commit();
            } else {
                $db->rollBack();
            }

            return $result;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function updateProduct(int $id, array $data): bool
    {
        $this->db->beginTransaction();
    
        try {
            $product = new Product($data);
            $product->id = $id;
            $result = $product->update();
    
            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }
    
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteProduct(int $id): bool
    {
        $this->db->beginTransaction();
    
        try {
            $product = new Product(['id' => $id]);
            $result = $product->delete();
    
            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }
    
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findProductByKeyWord($keyword, $pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;

        $query = "SELECT * FROM products WHERE name LIKE '%$keyword%'";
        $totalCount = Query::getCount("SELECT * FROM products WHERE name LIKE '%$keyword%'"); 
        $pagination = Pagination::paginate($limit, $page, $totalCount);

        // Fetch products with pagination
        $offset = $pagination['offset'];
        $query = Query::get('products', [], ['name' => $keyword], $limit, $offset);
        $req = $this->db->query($query)->fetchAll();
        
        $list = array_map(function ($item) {
            return new Product($item);
        }, $products);

        $result = [
            'list' => $list,
            'pagination' => $pagination
        ];

        return $result;
    }

    public function getProductNumber() : int
    {
        return Query::getCount("SELECT * FROM products");
    }
}