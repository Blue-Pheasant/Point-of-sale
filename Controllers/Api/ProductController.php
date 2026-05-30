<?php

namespace app\Controllers\Api;

use app\Core\Request;
use app\Models\Product;
use app\Services\ProductService;

/**
 * JSON product endpoints (roadmap T23).
 *
 * @package app\Controllers\Api
 */
class ProductController extends ApiController
{
    /** Default and maximum page sizes for the listing. */
    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT     = 100;

    public function __construct(private ProductService $productService)
    {
    }

    /**
     * `GET /api/products?q=&category=&min_price=&max_price=&page=&limit=`
     *
     * Returns a paginated, filtered list of products as JSON. Every filter is
     * forwarded to {@see ProductService::searchProducts()}, which binds each
     * value as a parameter — so injection attempts in the query string are
     * neutralised by the query builder, not by escaping here.
     *
     * @param Request $request The incoming request (query params).
     * @return void
     */
    public function index(Request $request): void
    {
        $filters = [
            'q'         => (string) ($request->getParam('q') ?? ''),
            'category'  => (string) ($request->getParam('category') ?? ''),
            'min_price' => $request->getParam('min_price'),
            'max_price' => $request->getParam('max_price'),
        ];

        $limit = (int) ($request->getParam('limit') ?? self::DEFAULT_LIMIT);
        $limit = max(1, min($limit, self::MAX_LIMIT));
        $page = max(1, (int) ($request->getParam('page') ?? 1));

        $result = $this->productService->searchProducts($filters, [
            'limit' => $limit,
            'page'  => $page,
        ]);

        $items = array_map(
            fn (Product $product) => $this->serialize($product),
            $result['list']
        );

        $this->respondPaginated($items, $result['pagination']);
    }

    /**
     * `GET /api/products/{id}` — a single product, or a 404 JSON error.
     *
     * @param Request $request The incoming request.
     * @param string $id The product id from the route.
     * @return void
     */
    public function show(Request $request, string $id): void
    {
        $product = $this->productService->getProductById($id);

        if ($product === null) {
            $this->error('Product not found.', 404);
            return;
        }

        $this->respond($this->serialize($product));
    }

    /**
     * Maps a {@see Product} to its public JSON shape.
     *
     * @return array<string, mixed>
     */
    private function serialize(Product $product): array
    {
        return [
            'id'             => $product->getId(),
            'category_id'    => $product->getCategoryId(),
            'name'           => $product->getName(),
            'price'          => (int) $product->getPrice(),
            'description'    => $product->getDescription(),
            'image_url'      => $product->getImageUrl(),
            'stock_quantity' => $product->getStockQuantity(),
        ];
    }
}
