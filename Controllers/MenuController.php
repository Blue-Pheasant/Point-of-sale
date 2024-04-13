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
use app\Auth\AuthUser;

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

    public function menu(Request $request)
    {
        $categoryId = $request->getParam('category_id');
        $currentUser = AuthUser::authUser();
        $cartId = $this->cartService->getCartIdFromUserId($currentUser->id);

        // Get the products
        $products = ($categoryId == '') 
            ? Product::getAllProducts() 
            : Product::getProductsByCategory($categoryId);

        $deletedItem = false;
        $updatedItem = false;

        // Get the items in the cart
        $items = $this->cartService->getCartItems($cartId);
        $categories = $this->categoryService->getAllCategories();

        return $this->render('menu', [
            'products' => $products, 
            'categories' => $categories, 
            'items' => $items,
            'deletedItem' => $deletedItem,
            'updatedItem' => $updatedItem,
            'cartId' => $cartId
        ]);
    }

    public function search(Request $request)
    {
        
        // Get the current user
        $currentUser = $this->getMiddleware()->authUser();
        $cartId = $this->cartService->getCartIdFromUserId($currentUser->id);

        // Initialize the deletedItem and updatedItem variables
        $deletedItem = false;
        $updatedItem = false;

        // Get the items in the cart
        $items = $this->cartService->getCartItems($cartId);
        $categories = $this->categoryService->getAllCategories();

        // Get the keyword from the request
        $keyword = $request->getBody()['keyword'];
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