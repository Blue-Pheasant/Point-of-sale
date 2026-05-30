<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Services\ProductService;
use PDOException;

/**
 * Integration tests proving transactional behaviour on the real (SQLite)
 * connection (roadmap T18): a commit persists, a mid-transaction failure rolls
 * everything back, and no partial writes survive.
 */
final class TransactionTest extends IntegrationTestCase
{
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCategory();
        $this->service = new ProductService();
    }

    public function testSuccessfulCreateCommits(): void
    {
        $this->service->createProduct([
            'category_id' => 'cat-1',
            'name'        => 'Flat White',
            'image_url'   => '',
            'price'       => 32000,
            'description' => 'Espresso with steamed milk, no foam.',
        ]);

        $this->assertSame(1, $this->rowCount());
    }

    public function testFailingInsertInsideTransactionRollsBackAndRethrows(): void
    {
        // Pre-insert a product with a known id, then force createProduct() to
        // collide on that primary key. The save() raises, the service rolls the
        // transaction back, and re-throws — so the table is left untouched.
        $this->service->createProduct([
            'id'          => 'fixed-id',
            'category_id' => 'cat-1',
            'name'        => 'Original',
            'image_url'   => '',
            'price'       => 10000,
            'description' => 'The first product to occupy the id.',
        ]);

        $this->assertSame(1, $this->rowCount());

        try {
            $this->service->createProduct([
                'id'          => 'fixed-id', // duplicate primary key → INSERT fails
                'category_id' => 'cat-1',
                'name'        => 'Duplicate',
                'image_url'   => '',
                'price'       => 20000,
                'description' => 'Should never be persisted because the id clashes.',
            ]);
            $this->fail('Expected a PDOException for the duplicate primary key.');
        } catch (PDOException $e) {
            // expected — the unique-id violation bubbles up after rollback.
        }

        // The failed insert must not have added a row, and the original is intact.
        $this->assertSame(1, $this->rowCount());
        $name = self::$pdo->query("SELECT name FROM products WHERE id = 'fixed-id'")->fetchColumn();
        $this->assertSame('Original', $name);
    }

    public function testManualRollbackDiscardsAllWrites(): void
    {
        self::$pdo->beginTransaction();
        self::$pdo->exec(
            "INSERT INTO products (id, category_id, name, price, description)
             VALUES ('tmp-1', 'cat-1', 'Temp', 1000, 'discarded')"
        );
        $this->assertSame(1, $this->rowCount());

        self::$pdo->rollBack();

        $this->assertSame(0, $this->rowCount());
    }

    private function rowCount(): int
    {
        return (int) self::$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
}
