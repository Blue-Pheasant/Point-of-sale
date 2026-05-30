<?php

namespace app\Services;

use app\Common\Pagination;
use app\Common\Query;
use app\Common\QueryBuilder;
use app\Exception\OutOfStockException;
use app\Models\Order;
use app\Models\OrderDetail;
use app\Models\OrderItem;
use PDO;

class OrderService
{
    public function __construct(private PDO $db)
    {
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

    /**
     * Total revenue across all completed orders, with size surcharges applied
     * through {@see PricingService::lineTotal()} (the single pricing source).
     *
     * @return int The total income in VND.
     */
    public function getTotalIncome(): int
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

    /**
     * Total payable amount for a single order (roadmap T22).
     *
     * Sums each order line through {@see PricingService::lineTotal()} — the same
     * pricing source as {@see self::getTotalIncome()} — so the charge sent to
     * the payment gateway always matches what the order is actually worth. The
     * order id is bound as a parameter.
     *
     * @param string $orderId The order to total.
     * @return int The order total in VND.
     */
    public function getOrderTotal(string $orderId): int
    {
        $rows = Query::getAll(
            'SELECT
                products.price        AS price,
                order_detail.quantity AS quantity,
                order_detail.size     AS size
            FROM
                order_detail
                INNER JOIN products ON order_detail.product_id = products.id
            WHERE
                order_detail.order_id = :orderId',
            ['orderId' => $orderId]
        );

        $total = 0;
        foreach ($rows as $row) {
            $total += PricingService::lineTotal(
                (float) $row['price'],
                (string) $row['size'],
                (int) $row['quantity']
            );
        }

        return (int) $total;
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

    /**
     * Revenue grouped by the day each completed order was placed (roadmap T21).
     *
     * The set is restricted to the last $days days. Each order line is joined to
     * its product so the size surcharge can be applied through
     * {@see PricingService::lineTotal()} — the same single source of truth used
     * by {@see self::getTotalIncome()}, so the figures always agree. The day
     * cut-off is bound as a parameter (never concatenated).
     *
     * @param int $days How many days back to include (defaults to 30).
     * @return array<int, array{date: string, revenue: int}> Ascending by date.
     */
    public function getRevenueByDay(int $days = 30): array
    {
        $days = max(1, $days);

        // The cut-off date is computed in PHP and bound as a value, because
        // MySQL prepared statements cannot bind the operand of `INTERVAL n DAY`
        // (it must be a literal). Binding a plain date keeps the query injection
        // -safe without that limitation.
        $since = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));

        $rows = Query::getAll(
            'SELECT
                DATE(orders.created_at) AS order_date,
                products.price          AS price,
                order_detail.quantity   AS quantity,
                order_detail.size       AS size
            FROM
                order_detail
                INNER JOIN products ON order_detail.product_id = products.id
                INNER JOIN orders   ON order_detail.order_id = orders.id
            WHERE
                orders.status = :status
                AND orders.deleted_at IS NULL
                AND orders.created_at >= :since',
            ['status' => Order::STATUS_DONE, 'since' => $since]
        );

        $byDate = [];
        foreach ($rows as $row) {
            $date = (string) $row['order_date'];
            $byDate[$date] = ($byDate[$date] ?? 0) + PricingService::lineTotal(
                (float) $row['price'],
                (string) $row['size'],
                (int) $row['quantity']
            );
        }

        ksort($byDate);

        $result = [];
        foreach ($byDate as $date => $revenue) {
            $result[] = ['date' => $date, 'revenue' => (int) $revenue];
        }

        return $result;
    }

    /**
     * The best-selling products across completed orders (roadmap T21).
     *
     * Ranks products by total quantity sold and also returns the revenue each
     * generated (size surcharges applied via {@see PricingService}). Limited to
     * the top $limit products; the limit is bound as an integer parameter.
     *
     * @param int $limit How many products to return (defaults to 5).
     * @return array<int, array{product_id: string, name: string, quantity: int, revenue: int}>
     */
    public function getTopProducts(int $limit = 5): array
    {
        $limit = max(1, $limit);

        $rows = Query::getAll(
            'SELECT
                products.id    AS product_id,
                products.name  AS name,
                products.price AS price,
                order_detail.quantity AS quantity,
                order_detail.size     AS size
            FROM
                order_detail
                INNER JOIN products ON order_detail.product_id = products.id
                INNER JOIN orders   ON order_detail.order_id = orders.id
            WHERE
                orders.status = :status
                AND orders.deleted_at IS NULL',
            ['status' => Order::STATUS_DONE]
        );

        $totals = [];
        foreach ($rows as $row) {
            $id = (string) $row['product_id'];
            if (!isset($totals[$id])) {
                $totals[$id] = [
                    'product_id' => $id,
                    'name'       => (string) $row['name'],
                    'quantity'   => 0,
                    'revenue'    => 0,
                ];
            }

            $totals[$id]['quantity'] += (int) $row['quantity'];
            $totals[$id]['revenue']  += (int) PricingService::lineTotal(
                (float) $row['price'],
                (string) $row['size'],
                (int) $row['quantity']
            );
        }

        usort($totals, fn ($a, $b) => $b['quantity'] <=> $a['quantity']);

        return array_slice(array_values($totals), 0, $limit);
    }

    /**
     * The average order value (AOV) across completed orders (roadmap T21).
     *
     * Computed as total completed revenue divided by the number of distinct
     * completed orders, reusing {@see self::getTotalIncome()} so the revenue
     * basis matches the dashboard total exactly.
     *
     * @return int The AOV in VND (0 when there are no completed orders).
     */
    public function getAverageOrderValue(): int
    {
        $orderCount = $this->getTotalOrderNumber(Order::STATUS_DONE);
        if ($orderCount === 0) {
            return 0;
        }

        return (int) ($this->getTotalIncome() / $orderCount);
    }
}
