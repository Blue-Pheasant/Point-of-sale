<?php

namespace app\Services;

use app\Common\Pagination;
use app\Common\Query;
use app\Common\QueryBuilder;
use app\Core\Database;
use app\Exception\OutOfStockException;
use app\Models\Order;
use app\Models\OrderDetail;
use app\Models\OrderItem;
use PDO;

class OrderService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Places an order and deducts stock atomically (roadmap T20).
     *
     * The order row, its detail rows and the per-product stock decrements all
     * happen inside a single transaction. Stock is decremented with a guarded
     * UPDATE (`stock_quantity >= :qty`) so two concurrent orders can never
     * oversell the last unit: if the guard matches no row, the whole
     * transaction is rolled back and an {@see OutOfStockException} is thrown.
     *
     * @param array<string, mixed> $orderData Order fields: user_id, payment_method,
     *        delivery_name, delivery_phone, delivery_address.
     * @param array<int, array{product_id: string, quantity: int, size?: string, note?: string}> $items
     *        The cart items to turn into order details.
     * @return string The id of the created order.
     *
     * @throws OutOfStockException When any item cannot be satisfied from stock.
     */
    public function placeOrder(array $orderData, array $items): string
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Không thể đặt hàng với giỏ hàng trống.');
        }

        $this->db->beginTransaction();

        try {
            $order = new Order(array_merge($orderData, ['status' => Order::STATUS_PROCESSING]));
            $order->save();

            foreach ($items as $item) {
                $productId = (string) $item['product_id'];
                $quantity  = (int) $item['quantity'];

                $this->decrementStock($productId, $quantity);

                $detail = new OrderDetail([
                    'product_id' => $productId,
                    'order_id'   => $order->id,
                    'quantity'   => $quantity,
                    'note'       => (string) ($item['note'] ?? ''),
                    'size'       => (string) ($item['size'] ?? ''),
                ]);
                $detail->save();
            }

            $this->db->commit();

            return $order->id;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Decrements a product's stock by $quantity, refusing to go negative.
     *
     * Uses a single guarded, param-bound UPDATE so the check and the decrement
     * are one atomic operation (race-safe even when stock is 1). When the guard
     * matches no row — out of stock or insufficient quantity — it raises an
     * {@see OutOfStockException} for the caller's transaction to roll back.
     *
     * @throws OutOfStockException When there is not enough stock.
     */
    private function decrementStock(string $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Số lượng đặt hàng phải lớn hơn 0.');
        }

        $statement = $this->db->prepare(
            'UPDATE products
                SET stock_quantity = stock_quantity - :qty
              WHERE id = :id AND stock_quantity >= :guard'
        );
        $statement->bindValue(':qty', $quantity, PDO::PARAM_INT);
        $statement->bindValue(':guard', $quantity, PDO::PARAM_INT);
        $statement->bindValue(':id', $productId, PDO::PARAM_STR);
        $statement->execute();

        if ($statement->rowCount() === 0) {
            throw new OutOfStockException(
                $productId,
                'Sản phẩm không đủ tồn kho để đặt hàng.'
            );
        }
    }

    public function getAllOrders($pagerCondition, $status): array
    {
        return Pagination::paginateResults(
            QueryBuilder::table('orders')
                ->where('status', $status)
                ->whereRaw('deleted_at IS NULL'),
            (int) $pagerCondition['limit'],
            (int) $pagerCondition['page'],
            fn ($item) => new Order($item)
        );
    }

    public function getOrderByUserId($userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = :user_id AND deleted_at IS NULL');
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
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new Order($result) : null;
    }

    public function getOrderItems($orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result ? $result : [];
    }

    public function acceptOrder($orderId): bool
    {
        return $this->setOrderStatus($orderId, Order::STATUS_ACCEPTED);
    }

    public function rejectOrder($orderId): bool
    {
        return $this->setOrderStatus($orderId, Order::STATUS_REJECTED);
    }

    /**
     * Sets an order's status (param-bound). Returns false (and logs) on error.
     */
    private function setOrderStatus($orderId, string $status): bool
    {
        try {
            return QueryBuilder::table('orders')
                ->where('id', $orderId)
                ->update(['status' => $status]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getTotalOrderNumber($status = ''): int
    {
        $query = 'SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL';
        $params = [];
        if ($status) {
            $query .= ' AND status = :status';
            $params['status'] = $status;
        }

        $stmt = Query::prepare($query, $params);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getTotalInCome(): int
    {
        $req = Query::getAll(
            'SELECT
                products.price,
                order_detail.quantity,
                order_detail.size
            FROM
                order_detail
                INNER JOIN products ON order_detail.product_id = products.id
                INNER JOIN orders ON order_detail.order_id = orders.id
            WHERE
                orders.status = :status',
            ['status' => Order::STATUS_DONE]
        );

        $totalIncome = 0;
        foreach ($req as $item) {
            $totalIncome += PricingService::lineTotal(
                (float) $item['price'],
                (string) $item['size'],
                (int) $item['quantity']
            );
        }

        return (int) $totalIncome;
    }

    public function getOrderItemsByOrderId($orderId)
    {
        $list = [];
        $req = Query::getAll(
            'SELECT *
            FROM order_detail JOIN products ON order_detail.product_id = products.id
            WHERE order_detail.order_id = :orderId',
            ['orderId' => $orderId]
        );

        foreach ($req as $item) {
            $list[] = new OrderItem($item);
        }

        return $list;
    }
}
