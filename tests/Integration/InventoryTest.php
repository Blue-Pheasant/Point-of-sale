<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Exception\OutOfStockException;
use app\Services\OrderService;
use app\Services\ProductService;

/**
 * Integration tests for the inventory feature (roadmap T20): transactional
 * stock deduction on order placement, blocking when out of stock, rollback on
 * failure, and the race condition at stock = 1.
 */
final class InventoryTest extends IntegrationTestCase
{
    private OrderService $orderService;
    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCategory();
        $this->seedUser('user-1');
        $this->orderService = new OrderService(self::$pdo);
        $this->productService = new ProductService(self::$pdo);
    }

    /** @return array<string, mixed> */
    private function orderData(): array
    {
        return [
            'user_id'          => 'user-1',
            'payment_method'   => 'cash',
            'delivery_name'    => 'Test Buyer',
            'delivery_phone'   => '0900000000',
            'delivery_address' => '1 Test Street',
        ];
    }

    private function stockOf(string $productId): int
    {
        return (int) self::$pdo
            ->query("SELECT stock_quantity FROM products WHERE id = '$productId'")
            ->fetchColumn();
    }

    public function testPlacingOrderDeductsStock(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 10]);

        $orderId = $this->orderService->placeOrder($this->orderData(), [
            ['product_id' => $id, 'quantity' => 3, 'size' => 'Small'],
        ]);

        $this->assertNotSame('', $orderId);
        $this->assertSame(7, $this->stockOf($id));
        // The order and its detail row were persisted.
        $this->assertSame(1, (int) self::$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn());
        $this->assertSame(1, (int) self::$pdo->query('SELECT COUNT(*) FROM order_detail')->fetchColumn());
    }

    public function testDeductsStockForEveryLineItem(): void
    {
        $a = $this->seedProduct(['id' => 'p-a', 'stock_quantity' => 5]);
        $b = $this->seedProduct(['id' => 'p-b', 'stock_quantity' => 8]);

        $this->orderService->placeOrder($this->orderData(), [
            ['product_id' => $a, 'quantity' => 2, 'size' => 'Small'],
            ['product_id' => $b, 'quantity' => 5, 'size' => 'Large'],
        ]);

        $this->assertSame(3, $this->stockOf($a));
        $this->assertSame(3, $this->stockOf($b));
    }

    public function testOrderingMoreThanStockIsBlocked(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 2]);

        $this->expectException(OutOfStockException::class);

        try {
            $this->orderService->placeOrder($this->orderData(), [
                ['product_id' => $id, 'quantity' => 3, 'size' => 'Small'],
            ]);
        } finally {
            // Even after the exception, nothing was committed.
            $this->assertSame(2, $this->stockOf($id));
            $this->assertSame(0, (int) self::$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn());
            $this->assertSame(0, (int) self::$pdo->query('SELECT COUNT(*) FROM order_detail')->fetchColumn());
        }
    }

    public function testOrderingOutOfStockProductIsBlocked(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 0]);

        $this->expectException(OutOfStockException::class);
        $this->orderService->placeOrder($this->orderData(), [
            ['product_id' => $id, 'quantity' => 1, 'size' => 'Small'],
        ]);
    }

    public function testMultiItemOrderRollsBackWhenOneItemIsShort(): void
    {
        // First item succeeds, second has insufficient stock → the whole order,
        // including the first item's deduction, must roll back.
        $a = $this->seedProduct(['id' => 'p-a', 'stock_quantity' => 10]);
        $b = $this->seedProduct(['id' => 'p-b', 'stock_quantity' => 1]);

        try {
            $this->orderService->placeOrder($this->orderData(), [
                ['product_id' => $a, 'quantity' => 4, 'size' => 'Small'],
                ['product_id' => $b, 'quantity' => 2, 'size' => 'Small'],
            ]);
            $this->fail('Expected an OutOfStockException.');
        } catch (OutOfStockException $e) {
            $this->assertSame('p-b', $e->getProductId());
        }

        // Neither product was decremented; no order/detail rows survive.
        $this->assertSame(10, $this->stockOf($a));
        $this->assertSame(1, $this->stockOf($b));
        $this->assertSame(0, (int) self::$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn());
        $this->assertSame(0, (int) self::$pdo->query('SELECT COUNT(*) FROM order_detail')->fetchColumn());
    }

    public function testRaceAtStockOneAllowsExactlyOneOrder(): void
    {
        // The guarded UPDATE makes "check + decrement" atomic, so the last unit
        // can be sold exactly once. The second attempt finds stock = 0.
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 1]);

        $firstOrderId = $this->orderService->placeOrder($this->orderData(), [
            ['product_id' => $id, 'quantity' => 1, 'size' => 'Small'],
        ]);
        $this->assertNotSame('', $firstOrderId);
        $this->assertSame(0, $this->stockOf($id));

        $this->expectException(OutOfStockException::class);
        $this->orderService->placeOrder($this->orderData(), [
            ['product_id' => $id, 'quantity' => 1, 'size' => 'Small'],
        ]);
    }

    public function testEmptyCartIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->orderService->placeOrder($this->orderData(), []);
    }

    // -- ProductService stock administration --------------------------------

    public function testSetStockOverwritesQuantity(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 4]);

        $this->assertTrue($this->productService->setStock($id, 25));
        $this->assertSame(25, $this->stockOf($id));
    }

    public function testSetStockClampsNegativeToZero(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 4]);

        $this->productService->setStock($id, -5);
        $this->assertSame(0, $this->stockOf($id));
    }

    public function testAdjustStockAddsAndSubtracts(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 10]);

        $this->productService->adjustStock($id, 5);
        $this->assertSame(15, $this->stockOf($id));

        $this->productService->adjustStock($id, -8);
        $this->assertSame(7, $this->stockOf($id));
    }

    public function testAdjustStockNeverGoesNegative(): void
    {
        $id = $this->seedProduct(['id' => 'p-1', 'stock_quantity' => 3]);

        $this->productService->adjustStock($id, -10);
        $this->assertSame(0, $this->stockOf($id));
    }
}
