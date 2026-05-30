<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Models\Product;
use app\Services\ProductService;

/**
 * Integration tests for {@see ProductService} against a real (SQLite) database
 * (roadmap T18): CRUD, keyword search and pagination.
 */
final class ProductServiceTest extends IntegrationTestCase
{
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCategory();
        $this->service = new ProductService();
    }

    public function testGetProductByIdReturnsHydratedModel(): void
    {
        $id = $this->seedProduct(['name' => 'Latte', 'price' => 30000]);

        $product = $this->service->getProductById($id);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('Latte', $product->name);
        $this->assertSame($id, $product->getId());
    }

    public function testGetProductByIdReturnsNullWhenMissing(): void
    {
        $this->assertNull($this->service->getProductById('does-not-exist'));
    }

    public function testCreateProductPersistsRow(): void
    {
        $ok = $this->service->createProduct([
            'category_id' => 'cat-1',
            'name'        => 'Cold Brew',
            'image_url'   => '',
            'price'       => 40000,
            'description' => 'Slow-steeped overnight coffee.',
        ]);

        $this->assertTrue($ok);
        $this->assertSame(1, (int) self::$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn());
    }

    public function testSearchProductsMatchesByKeyword(): void
    {
        $this->seedProduct(['id' => 'p-mocha', 'name' => 'Mocha']);
        $this->seedProduct(['id' => 'p-tea', 'name' => 'Green Tea']);

        $result = $this->service->searchProducts('Mocha', ['limit' => 10, 'page' => 1]);

        $this->assertCount(1, $result['list']);
        $this->assertSame('Mocha', $result['list'][0]->name);
    }

    public function testSearchReturnsEmptyWhenNoMatch(): void
    {
        $this->seedProduct(['name' => 'Mocha']);

        $result = $this->service->searchProducts('Sandwich', ['limit' => 10, 'page' => 1]);

        $this->assertSame([], $result['list']);
        $this->assertSame(0, $result['pagination']['totalCount']);
    }

    public function testGetAllProductsPaginatesAndExcludesSoftDeleted(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->seedProduct(['id' => "p$i", 'name' => "Product $i"]);
        }
        // Soft-delete one — it must drop out of the listing and the count.
        self::$pdo->exec("UPDATE products SET deleted_at = CURRENT_TIMESTAMP WHERE id = 'p0'");

        $page1 = $this->service->getAllProducts(['limit' => 2, 'page' => 1]);

        $this->assertCount(2, $page1['list']);
        $this->assertSame(4, $page1['pagination']['totalCount']);
        $this->assertEquals(2, $page1['pagination']['lastPageNum']);
        $this->assertTrue($page1['pagination']['hasNext']);
    }

    public function testGetProductNumberCountsRows(): void
    {
        $this->seedProduct(['id' => 'a']);
        $this->seedProduct(['id' => 'b']);

        $this->assertSame(2, $this->service->getProductNumber());
    }
}
