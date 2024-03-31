<?php

namespace app\services;

use app\core\Database;
use app\models\Order;
use app\models\OrderItem;
use app\Common\Pagination;
use app\Common\Query;
use PDO;

class OrderService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllOrders($pagerCondition, $status) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
        
        $totalCount = Query::getCount("SELECT * FROM orders WHERE status = '$status'");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        
        $offset = $pagination['offset'];
        $query = Query::get('orders', [], [], $limit, $offset);
        
        $req = $this->db->query($query)->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new Order($item);
        }

        $result = [
            'list' => $list,
            'pagination' => $pagination
        ];

        return $result;
    }

    public function getOrderByUserId($userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->execute();
    
        $req = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $list = [];

        foreach ($req as $item) {
            $list[] = new Order($item);
        }
    
        return $list;
    }

    public function getOrderById($id): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new Order($result) : null;
    }

    public function getOrderItems($orderId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $result ? $result : [];
    }

    public function acceptOrder($orderId): bool
    {
        try {
            $this->db->beginTransaction();
        
            $stmt = $this->db->prepare("UPDATE orders SET status = 'accepted' WHERE id = :id");
            $stmt->bindValue(':id', $orderId, PDO::PARAM_STR);
            $stmt->execute();
        
            $this->db->commit();
        
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function rejectOrder($orderId): bool
    {
        try {
            $this->db->beginTransaction();
        
            $stmt = $this->db->prepare("UPDATE orders SET status = 'rejected' WHERE id = :id");
            $stmt->bindValue(':id', $orderId, PDO::PARAM_STR);
            $stmt->execute();
        
            $this->db->commit();
        
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}