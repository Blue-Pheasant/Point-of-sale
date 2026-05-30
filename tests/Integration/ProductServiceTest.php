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
        $this->service = new ProductService(self::$pdo);
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

    public function testSearchFiltersByCategory(): void
    {
        $this->seedCategory('cat-2', 'Tea');
        $this->seedProduct(['id' => 'p-coffee', 'name' => 'Coffee', 'category_id' => 'cat-1']);
        $this->seedProduct(['id' => 'p-tea', 'name' => 'Tea', 'category_id' => 'cat-2']);

        $result = $this->service->searchProducts(['category' => 'cat-2'], ['limit' => 10, 'page' => 1]);

        $this->assertCount(1, $result['list']);
        $this->assertSame('p-tea', $result['list'][0]->getId());
    }

    public function testSearchFiltersByPriceRange(): void
    {
        $this->seedProduct(['id' => 'p-cheap', 'name' => 'Cheap', 'price' => 10000]);
        $this->seedProduct(['id' => 'p-mid', 'name' => 'Mid', 'price' => 25000]);
        $this->seedProduct(['id' => 'p-pricey', 'name' => 'Pricey', 'price' => 50000]);

        $result = $this->service->searchProducts(
            ['min_price' => 20000, 'max_price' => 30000],
            ['limit' => 10, 'page' => 1]
        );

        $this->assertCount(1, $result['list']);
        $this->assertSame('p-mid', $result['list'][0]->getId());
    }

    public function testSearchCombinesKeywordCategoryAndPrice(): void
    {
        $this->seedCategory('cat-2', 'Tea');
        $this->seedProduct(['id' => 'p1', 'name' => 'Iced Coffee', 'category_id' => 'cat-1', 'price' => 25000]);
        $this->seedProduct(['id' => 'p2', 'name' => 'Iced Coffee', 'category_id' => 'cat-2', 'price' => 25000]);
        $this->seedProduct(['id' => 'p3', 'name' => 'Iced Coffee', 'category_id' => 'cat-1', 'price' => 90000]);
        $this->seedProduct(['id' => 'p4', 'name' => 'Hot Coffee', 'category_id' => 'cat-1', 'price' => 25000]);

        $result = $this->service->searchProducts(
            ['q' => 'Iced', 'category' => 'cat-1', 'min_price' => 20000, 'max_price' => 30000],
            ['limit' => 10, 'page' => 1]
        );

        $this->assertCount(1, $result['list']);
        $this->assertSame('p1', $result['list'][0]->getId());
    }

    public function testSearchIgnoresBlankFiltersAndReturnsAllNonDeleted(): void
    {
        $this->seedProduct(['id' => 'p1', 'name' => 'One']);
        $this->seedProduct(['id' => 'p2', 'name' => 'Two']);

        $result = $this->service->searchProducts(
            ['q' => '', 'category' => '', 'min_price' => null, 'max_price' => null],
            ['limit' => 10, 'page' => 1]
        );

        $this->assertCount(2, $result['list']);
    }

    public function testSearchIsSafeAgainstInjectionInFilters(): void
    {
        $this->seedProduct(['id' => 'p1', 'name' => 'Latte']);

        // A SQL-injection attempt in the keyword must be treated as a literal
        // (param-bound), matching nothing — never executing as SQL.
        $result = $this->service->searchProducts(
            ['q' => "x' OR '1'='1"],
            ['limit' => 10, 'page' => 1]
        );

        $this->assertSame([], $result['list']);
        // The table is intact: a normal search still works afterwards.
        $this->assertCount(1, $this->service->searchProducts(['q' => 'Latte'], ['limit' => 10, 'page' => 1])['list']);
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
