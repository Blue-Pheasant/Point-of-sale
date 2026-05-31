<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Models\Order;
use app\Services\OrderService;

/**
 * Integration tests for {@see OrderService} against a real (SQLite) database
 * (roadmap T18): creation, counting, revenue and status transitions.
 */
final class OrderServiceTest extends IntegrationTestCase
{
    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCategory();
        $this->seedUser('user-1');
        $this->service = new OrderService(self::$pdo);
    }

    private function seedOrder(string $id, string $status): void
    {
        self::$pdo->prepare(
            'INSERT INTO orders (id, user_id, status, payment_method, delivery_name, delivery_phone, delivery_address)
             VALUES (:id, "user-1", :status, "cash", "Test", "0900000000", "1 Test St")'
        )->execute(['id' => $id, 'status' => $status]);
    }

    private function seedOrderDetail(string $orderId, string $productId, int $qty, string $size): void
    {
        self::$pdo->prepare(
            'INSERT INTO order_detail (id, product_id, order_id, quantity, size, note)
             VALUES (:id, :pid, :oid, :qty, :size, "")'
        )->execute([
            'id'   => 'od-' . $orderId . '-' . $productId,
            'pid'  => $productId,
            'oid'  => $orderId,
            'qty'  => $qty,
            'size' => $size,
        ]);
    }

    public function testGetOrderByIdReturnsModel(): void
    {
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);

        $order = $this->service->getOrderById('o-1');

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame('user-1', $order->getUserId());
    }

    public function testGetTotalOrderNumberCountsAllNonDeleted(): void
    {
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);
        $this->seedOrder('o-2', Order::STATUS_DONE);

        $this->assertSame(2, $this->service->getTotalOrderNumber());
    }

    public function testGetTotalOrderNumberFiltersByStatus(): void
    {
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);
        $this->seedOrder('o-2', Order::STATUS_DONE);
        $this->seedOrder('o-3', Order::STATUS_DONE);

        $this->assertSame(2, $this->service->getTotalOrderNumber(Order::STATUS_DONE));
    }

    public function testGetTotalOrderNumberExcludesSoftDeleted(): void
    {
        $this->seedOrder('o-1', Order::STATUS_DONE);
        self::$pdo->exec("UPDATE orders SET deleted_at = CURRENT_TIMESTAMP WHERE id = 'o-1'");

        $this->assertSame(0, $this->service->getTotalOrderNumber());
    }

    public function testGetTotalIncomeSumsLineTotalsForDoneOrders(): void
    {
        // Espresso base 25000; Medium adds 3000 → 28000 × 2 = 56000.
        $productId = $this->seedProduct(['id' => 'p-1', 'name' => 'Espresso', 'price' => 25000]);
        $this->seedOrder('o-done', Order::STATUS_DONE);
        $this->seedOrder('o-processing', Order::STATUS_PROCESSING);
        $this->seedOrderDetail('o-done', $productId, 2, 'Medium');
        // An item on a non-done order must NOT contribute to revenue.
        $this->seedOrderDetail('o-processing', $productId, 5, 'Large');

        $this->assertSame(56000, $this->service->getTotalIncome());
    }

    public function testAcceptOrderUpdatesStatus(): void
    {
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);

        $this->assertTrue($this->service->acceptOrder('o-1'));

        $status = self::$pdo->query("SELECT status FROM orders WHERE id = 'o-1'")->fetchColumn();
        $this->assertSame(Order::STATUS_ACCEPTED, $status);
    }

    public function testRejectOrderUpdatesStatus(): void
    {
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);

        $this->service->rejectOrder('o-1');

        $status = self::$pdo->query("SELECT status FROM orders WHERE id = 'o-1'")->fetchColumn();
        $this->assertSame(Order::STATUS_REJECTED, $status);
    }

    public function testGetOrderByUserIdReturnsOnlyTheirOrders(): void
    {
        $this->seedUser('user-2', 'other@example.com');
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);
        self::$pdo->prepare(
            'INSERT INTO orders (id, user_id, status, payment_method, delivery_name, delivery_phone, delivery_address)
             VALUES ("o-2", "user-2", "processing", "cash", "T", "0", "x")'
        )->execute();

        $orders = $this->service->getOrderByUserId('user-1');

        $this->assertCount(1, $orders);
        $this->assertSame('o-1', $orders[0]->getId());
    }

    public function testGetRevenueByDayGroupsDoneOrdersByDate(): void
    {
        $productId = $this->seedProduct(['id' => 'p-1', 'name' => 'Espresso', 'price' => 25000]);
        $this->seedOrder('o-done', Order::STATUS_DONE);
        $this->seedOrder('o-processing', Order::STATUS_PROCESSING);
        // Small adds nothing → 25000 × 2 = 50000 for the done order on today.
        $this->seedOrderDetail('o-done', $productId, 2, 'Small');
        // A non-done order must not contribute to revenue.
        $this->seedOrderDetail('o-processing', $productId, 5, 'Large');

        $today = date('Y-m-d');
        $revenue = $this->service->getRevenueByDay();

        $this->assertCount(1, $revenue);
        $this->assertSame($today, $revenue[0]['date']);
        $this->assertSame(50000, $revenue[0]['revenue']);
    }

    public function testGetTopProductsRanksByQuantitySold(): void
    {
        $coffee = $this->seedProduct(['id' => 'p-coffee', 'name' => 'Coffee', 'price' => 25000]);
        $tea = $this->seedProduct(['id' => 'p-tea', 'name' => 'Tea', 'price' => 20000]);
        $this->seedOrder('o-1', Order::STATUS_DONE);
        $this->seedOrderDetail('o-1', $tea, 7, 'Small');
        $this->seedOrderDetail('o-1', $coffee, 3, 'Small');

        $top = $this->service->getTopProducts();

        $this->assertCount(2, $top);
        $this->assertSame('p-tea', $top[0]['product_id']);
        $this->assertSame(7, $top[0]['quantity']);
        $this->assertSame(140000, $top[0]['revenue']);
        $this->assertSame('p-coffee', $top[1]['product_id']);
    }

    public function testGetTopProductsHonoursTheLimit(): void
    {
        $this->seedOrder('o-1', Order::STATUS_DONE);
        foreach (range(1, 3) as $i) {
            $pid = $this->seedProduct(['id' => "p-$i", 'name' => "Product $i", 'price' => 1000 * $i]);
            $this->seedOrderDetail('o-1', $pid, $i, 'Small');
        }

        $this->assertCount(2, $this->service->getTopProducts(2));
    }

    public function testGetAverageOrderValueDividesRevenueByDoneOrderCount(): void
    {
        $productId = $this->seedProduct(['id' => 'p-1', 'name' => 'Espresso', 'price' => 25000]);
        // Two done orders: 25000 + 50000 = 75000 over 2 orders → AOV 37500.
        $this->seedOrder('o-1', Order::STATUS_DONE);
        $this->seedOrder('o-2', Order::STATUS_DONE);
        $this->seedOrderDetail('o-1', $productId, 1, 'Small');
        $this->seedOrderDetail('o-2', $productId, 2, 'Small');

        $this->assertSame(37500, $this->service->getAverageOrderValue());
    }

    public function testGetAverageOrderValueIsZeroWithoutDoneOrders(): void
    {
        $this->seedOrder('o-1', Order::STATUS_PROCESSING);

        $this->assertSame(0, $this->service->getAverageOrderValue());
    }
}
