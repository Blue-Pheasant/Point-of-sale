<?php

namespace App\Controllers;

use App\Controllers\SiteController;
use App\Models\Category;
use App\Models\Product;
use App\Core\Request;
use App\Core\Application;
use App\Middlewares\AuthMiddleware;
use App\Models\CartItem;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\ProductService;

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