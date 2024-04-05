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
        
        $totalCount = Query::getCount("SELECT * FROM orders WHERE status = '$status' AND deleted_at IS NULL");
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
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = :user_id AND deleted_at IS NULL");
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

    public function getTotalOrderNumber($status = ''): int
    {
        $query = "SELECT COUNT(*) FROM users";
        if ($status) {
            $query .= " WHERE status = '$status'";
        }
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn();
    }

    public function getTotalInCome(): int
    {
        $query = "
            SELECT
                products.price,
                order_detail.quantity,
                order_detail.size
            FROM
                order_detail
                INNER JOIN products ON order_detail.product_id = products.id
                INNER JOIN orders ON order_detail.order_id = orders.id
            WHERE
                orders.status = 'done'";
    
        $stmt = $this->db->query($query);
        $req = $stmt->fetchAll();
        $totalIncome = 0;
    
        foreach ($req as $item) {
            $unitPrice = $item['price'];
            if($item['size'] == 'Medium') {
                $unitPrice += 3000;
            } else if($item['size'] == 'Large') {
                $unitPrice += 6000;
            }
            $totalIncome += $unitPrice * $item['quantity'];
        }
    
        return $totalIncome;
    }

    public function getOrderItemsByOrderId($orderId)
    {
        $list = [];
        $req = $this->db->query(
            "SELECT *
            FROM order_detail JOIN products ON order_detail.product_id = products.id 
            WHERE order_detail.order_id = '$orderId';"
        )->fetchAll();

        foreach ($req as $item) {
            $list[] = new OrderItem($item);
        }

        return $list;
    }
}