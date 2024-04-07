<?php

namespace app\Controllers;

use app\Controllers\SiteController;
use app\Models\Category;
use app\Models\Product;
use app\Core\Request;
use app\Core\Application;
use app\Middlewares\AuthMiddleware;
use app\Models\CartItem;
use app\Services\CartService;
use app\Services\CategoryService;
use app\Services\ProductService;

class MenuController extends SiteController
{
    private CartService $cartService;
    private CategoryService $categoryService;
    private ProductService $productService;

    public function __construct()
    {
        $this->registerMiddleware(AuthMiddleware::class, ['menu', 'search']);
        $this->cartService = new CartService();
        $this->categoryService = new CategoryService();
        $this->productService = new ProductService();
    }

    public function menu()
    {
        $categoryId = Application::$app->request->getParam('category_id');
        $cartId = Application::$app->cart->id;
        if ($categoryId == '') {
            $products = Product::getAllProducts();
        } else {
            $products = Product::getProductsByCategory($categoryId);
        }
        $deletedItem = false;
        $updatedItem = false;
        $items = $this->cartService->getCartItems($cartId);
        $categories = $this->categoryService->getAllCategories();

        return $this->render('menu', [
            'products' => $products, 
            'categories' => $categories, 
            'items' => $items,
            'deletedItem' => $deletedItem,
            'updatedItem' => $updatedItem
        ]);
    }

    public function search(Request $request)
    {
        $cartId = Application::$app->cart->id;
        $deletedItem = false;
        $updatedItem = false;
        $items = $this->cartService->getCartItems($cartId);
        $categories = $this->categoryService->getAllCategories();

        $body = Application::$app->request->getBody();
        $keyword = $body['keyword'];
        $products = Product::getProductsByKeyword($keyword);
        return $this->render('menu', [
            'products' => $products, 
            'categories' => $categories, 
            'items' => $items,
            'deletedItem' => $deletedItem,
            'updatedItem' => $updatedItem
        ]);
    }
}