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
        $totalCount = Query::getCount("SELECT * FROM products");
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
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

    public function getProductById($id) : Product
    {
        $req = $this->db->query("SELECT * FROM products WHERE id = '$id'")->fetchAll()[0];
        $product = new Product($req);
        return $product;
    }

    public function getProductCategory($categoryId, $pagerCondition = []) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
        $query = "SELECT * FROM products WHERE category_id = '$categoryId'";
        $totalCount = Query::getCount("SELECT * FROM products WHERE category_id = '$categoryId'");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        $offset = $pagination['offset'];
        $query = Query::get('products', ['category_id' => $categoryId], [], $limit, $offset);
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

    public function createProduct($data) : bool
    {
        $product = new Product($data);
        return $product->save();
    }

    public function updateProduct($id, $data) : bool
    {
        $product = new Product($data);
        $product->id = $id;
        return $product->update($product);
    }

    public function deleteProduct($id) : bool
    {
        $product = new Product(['id' => $id]);
        return $product->delete();
    }

    public function findProductByKeyWord($keyword, $pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
        $query = "SELECT * FROM products WHERE name LIKE '%$keyword%'";
        $totalCount = Query::getCount("SELECT * FROM products WHERE name LIKE '%$keyword%'");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        $offset = $pagination['offset'];
        $query = Query::get('products', [], ['name' => $keyword], $limit, $offset);
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

    public function getProductNumber() : int
    {
        return Query::getCount("SELECT * FROM products");
    }
}