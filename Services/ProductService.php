<?php

namespace app\Services;

use app\Common\Pagination;
use app\Common\Query;
use app\Common\QueryBuilder;
use app\Models\Product;
use PDO;

class ProductService
{
    public function __construct(private PDO $db)
    {
    }

    public function getAllProducts($pagerCondition): array
    {
        return Pagination::paginateResults(
            QueryBuilder::table('products')->whereRaw('deleted_at IS NULL'),
            (int) $pagerCondition['limit'],
            (int) $pagerCondition['page'],
            fn ($item) => new Product($item)
        );
    }

    public function getProductById($id): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new Product($result) : null;
    }

    public function getProductCategory($categoryId, $pagerCondition = []): array
    {
        return Pagination::paginateResults(
            QueryBuilder::table('products')->where('category_id', $categoryId),
            (int) ($pagerCondition['limit'] ?? 10),
            (int) ($pagerCondition['page'] ?? 1),
            fn ($item) => new Product($item)
        );
    }

    public function createProduct(array $data): bool
    {
        $this->db->beginTransaction();

        try {
            $product = new Product($data);
            $result = $product->save();

            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }

            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateProduct(int $id, array $data): bool
    {
        $this->db->beginTransaction();

        try {
            $product = new Product($data);
            $product->id = $id;
            $result = $product->update();

            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }

            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteProduct(int $id): bool
    {
        $this->db->beginTransaction();

        try {
            $product = new Product(['id' => $id]);
            $result = $product->delete();

            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }

            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Searches products whose name matches the given keyword, with pagination.
     *
     * Uses the param-bound {@see QueryBuilder} (LIKE wildcards are bound, never
     * concatenated), so the search is safe against SQL injection.
     *
     * @param string $keyword The keyword to match against the product name.
     * @param array<string, mixed> $pagerCondition The pagination conditions (limit, page).
     * @return array{list: array<int, Product>, pagination: array<string, mixed>}
     */
    public function searchProducts(string $keyword, array $pagerCondition): array
    {
        return Pagination::paginateResults(
            QueryBuilder::table('products')->whereLike('name', $keyword),
            (int) $pagerCondition['limit'],
            (int) $pagerCondition['page'],
            fn ($item) => new Product($item)
        );
    }

    /**
     * @deprecated Use {@see self::searchProducts()} instead. Kept as a
     *     backward-compatible alias for callers of the old name.
     *
     * @param string $keyword
     * @param array<string, mixed> $pagerCondition
     * @return array{list: array<int, Product>, pagination: array<string, mixed>}
     */
    public function findProductByKeyWord($keyword, $pagerCondition): array
    {
        return $this->searchProducts((string) $keyword, $pagerCondition);
    }

    public function getProductNumber(): int
    {
        return Query::getCount('SELECT * FROM products');
    }

    /**
     * Sets a product's on-hand stock to an absolute value (roadmap T20).
     *
     * Used by the admin stock form. The value is clamped at 0 so a typo can
     * never store negative stock, and the update is param-bound.
     *
     * @param int $quantity The new on-hand quantity (negatives clamp to 0).
     * @return bool True when the update succeeds.
     */
    public function setStock(string $id, int $quantity): bool
    {
        return QueryBuilder::table('products')
            ->where('id', $id)
            ->update(['stock_quantity' => max(0, $quantity)]);
    }

    /**
     * Adjusts a product's stock by a (positive or negative) delta, never
     * dropping below 0. Convenience for restocking or manual corrections.
     *
     * @return bool True when the update succeeds.
     */
    public function adjustStock(string $id, int $delta): bool
    {
        $product = $this->getProductById($id);
        if ($product === null) {
            return false;
        }

        return $this->setStock($id, $product->getStockQuantity() + $delta);
    }
}
