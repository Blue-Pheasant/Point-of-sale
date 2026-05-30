<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Models\Cart;
use app\Services\CartService;

/**
 * Integration tests for {@see CartService} against a real (SQLite) database
 * (roadmap T18): cart lookup, item join and checkout.
 */
final class CartServiceTest extends IntegrationTestCase
{
    private CartService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCategory();
        $this->seedUser('user-1');
        $this->service = new CartService();
    }

    private function seedCart(string $id = 'cart-1', string $userId = 'user-1', string $status = 'active'): void
    {
        self::$pdo->prepare('INSERT INTO cart (id, user_id, status) VALUES (:id, :uid, :status)')
            ->execute(['id' => $id, 'uid' => $userId, 'status' => $status]);
    }

    private function seedCartItem(string $id, string $cartId, string $productId, int $qty): void
    {
        self::$pdo->prepare(
            'INSERT INTO cart_item (id, cart_id, product_id, quantity, size, note)
             VALUES (:id, :cid, :pid, :qty, "Small", "")'
        )->execute(['id' => $id, 'cid' => $cartId, 'pid' => $productId, 'qty' => $qty]);
    }

    public function testGetCartByUserIdReturnsModel(): void
    {
        $this->seedCart();

        $cart = $this->service->getCartByUserId('user-1');

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertSame('cart-1', $cart->id);
    }

    public function testGetCartByUserIdReturnsNullWhenAbsent(): void
    {
        $this->assertNull($this->service->getCartByUserId('user-1'));
    }

    public function testGetCartIdFromUserId(): void
    {
        $this->seedCart('cart-9');

        $this->assertSame('cart-9', $this->service->getCartIdFromUserId('user-1'));
    }

    public function testGetCartItemsJoinsProductData(): void
    {
        $productId = $this->seedProduct(['id' => 'p-1', 'name' => 'Cappuccino', 'price' => 35000]);
        $this->seedCart('cart-1');
        $this->seedCartItem('ci-1', 'cart-1', $productId, 2);

        $items = $this->service->getCartItems('cart-1');

        $this->assertCount(1, $items);
        $this->assertSame('Cappuccino', $items[0]->name);
        $this->assertSame(2, $items[0]->quantity);
    }

    public function testCheckOutCartMarksCartDone(): void
    {
        $this->seedCart('cart-1', 'user-1', 'active');

        $this->service->checkOutCart('cart-1');

        $status = self::$pdo->query("SELECT status FROM cart WHERE id = 'cart-1'")->fetchColumn();
        $this->assertSame('done', $status);
    }
}
