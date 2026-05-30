<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Models\Product;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the inventory helpers on {@see Product} (roadmap T20):
 * out-of-stock and low-stock detection.
 */
final class ProductStockTest extends TestCase
{
    public function testOutOfStockWhenQuantityZero(): void
    {
        $product = new Product(['stock_quantity' => 0]);

        $this->assertTrue($product->isOutOfStock());
    }

    public function testNotOutOfStockWhenQuantityPositive(): void
    {
        $product = new Product(['stock_quantity' => 3]);

        $this->assertFalse($product->isOutOfStock());
    }

    public function testLowStockAtOrBelowThreshold(): void
    {
        $product = new Product(['stock_quantity' => 5, 'low_stock_threshold' => 5]);
        $this->assertTrue($product->isLowStock());

        $product = new Product(['stock_quantity' => 4, 'low_stock_threshold' => 5]);
        $this->assertTrue($product->isLowStock());
    }

    public function testNotLowStockAboveThreshold(): void
    {
        $product = new Product(['stock_quantity' => 6, 'low_stock_threshold' => 5]);

        $this->assertFalse($product->isLowStock());
    }

    public function testOutOfStockIsNotReportedAsLowStock(): void
    {
        // Zero stock is "out of stock", never merely "low" — even at threshold 0.
        $product = new Product(['stock_quantity' => 0, 'low_stock_threshold' => 0]);

        $this->assertTrue($product->isOutOfStock());
        $this->assertFalse($product->isLowStock());
    }

    public function testGettersCastToInt(): void
    {
        $product = new Product(['stock_quantity' => '7', 'low_stock_threshold' => '2']);

        $this->assertSame(7, $product->getStockQuantity());
        $this->assertSame(2, $product->getLowStockThreshold());
    }
}
