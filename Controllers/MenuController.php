<?php

namespace app\Controllers;

use app\Models\Product;
use app\Core\Request;
use app\Middlewares\AuthMiddleware;
use app\Services\CartService;
use app\Services\CategoryService;
use app\Services\ProductService;
use app\Auth\AuthUser;

/**
 * Class MenuController
 *
 * This class is responsible for handling the menu operations of the application.
 * It extends the base SiteController class and uses services for cart, categories, and products.
 *
 * @package app\Controllers
 */
class MenuController extends SiteController
{
    /**
     * @var CartService $cartService An instance of CartService to handle cart-related operations.
     */
    private CartService $cartService;

    /**
     * @var CategoryService $categoryService An instance of CategoryService to handle category-related operations.
     */
    private CategoryService $categoryService;

    /**
     * @var ProductService $productService An instance of ProductService to handle product-related operations.
     */
    private ProductService $productService;

    /**
     * MenuController constructor.
     *
     * Initializes the services and registers the middleware.
     */
    public function __construct()
    {
        $this->registerMiddleware(AuthMiddleware::class, ['menu', 'search']);
        $this->cartService = new CartService();
        $this->categoryService = new CategoryService();
        $this->productService = new ProductService();
    }

    /**
     * Method menu
     *
     * Fetches the products and categories, and the items in the cart.
     * Sets the deletedItem and updatedItem variables to false.
     * Renders the 'menu' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function menu(Request $request): array|bool|string
    {
        $categoryId = $request->getParam('category_id');
        $currentUser = AuthUser::authUser();
        $cartId = $this->cartService->getCartIdFromUserId($currentUser->id);

        // Get the products
        $products = ($categoryId == '') 
            ? Product::getAllProducts() 
            : Product::getProductsByCategory($categoryId);

        // Get the items in the cart
        $items = $this->cartService->getCartItems($cartId);
        $categories = $this->categoryService->getAllCategories();

        return $this->render('menu', [
            'products' => $products, 
            'categories' => $categories, 
            'items' => $items,
            'deletedItem' => false,
            'updatedItem' => false,
            'cartId' => $cartId
        ]);
    }

    /**
     * Method search
     *
     * Fetches the products by keyword and renders the 'menu' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function search(Request $request): array|bool|string
    {
        
        // Get the current user
        $currentUser = AuthUser::authUser();
        $cartId = $this->cartService->getCartIdFromUserId($currentUser->id);

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
            'deletedItem' => false,
            'updatedItem' => false
        ]);
    }
}