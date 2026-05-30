<?php

namespace app\Services;

use app\Common\Pagination;
use app\Common\Query;
use app\Common\QueryBuilder;
use app\Core\Database;
use app\Models\Product;
use PDO;

class ProductService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
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
}
