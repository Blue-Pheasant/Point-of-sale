<?php
/*
    controllers/product.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Models\Product;
use app\Core\Request;
use app\Core\Session;
use app\Models\CartItem;
use app\Services\ProductService;
use app\Middlewares\AdminMiddleware;
use app\Middlewares\AuthMiddleware;
/**
 * Class ProductController
 *
 * This class is responsible for handling the product operations of the application.
 * It extends the base Controller class and uses services for products.
 * It also uses middleware for administrative tasks.
 *
 * @package app\Controllers
 */
class ProductController extends Controller
{
    /**
     * @var ProductService $productService An instance of ProductService to handle product-related operations.
     */
    protected ProductService $productService;

    /**
     * ProductController constructor.
     *
     * Initializes the services and registers the middleware.
     */
    public function __construct()
    {
        $this->productService = new ProductService();
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'create', 'delete', 'update', 'details']);
        $this->registerMiddleware(AuthMiddleware::class, ['product']);
    }

    /**
     * Method index
     *
     * Fetches all products and renders the 'products' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function index(): array|bool|string
    {
        $products = $this->productService->getAllProducts(['limit' => 10, 'page' => 1])['list'];
        $this->setLayout('admin');
        return $this->render('/admin/products/products', [
            'products' => $products
        ]);
    }

    /**
     * Method create
     *
     * Creates a new product and saves it to the database.
     * If the request method is 'post', it loads the data, validates it, and saves it.
     * If the request method is 'get', it renders the 'create_product' view.
     *
     * @param Request $request The request object containing the request parameters.
     * @return array|bool|string
     */
    public function create(Request $request): array|bool|string
    {
        $productModel = new Product();
        if ($request->getMethod() === 'post') {
            $productModel->loadData($request->getBody());
            if ($productModel->validate() && $productModel->save()) {
                $this->setFlash('success', 'Create product successfully');
                $this->redirect('/admin/products/details_product?id=' . $productModel->getId());
            } else {
                $this->setFlash('fail', 'Create product fail');
            }
        }
        
        // Fetch all products
        $products = Product::getAllProducts();

        $this->setLayout('admin');
        return $this->render('/admin/products/create_product', [
            'productModel' => $products
        ]);
    }

    /**
     * Method delete
     *
     * Deletes a product from the database.
     * If the request method is 'post', it deletes the product.
     * If the request method is 'get', it renders the 'delete_product' view.
     *
     * @param Request $request The request object containing the request parameters.
     * @return array|bool|string
     */
    public function delete(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $productModel = $this->productService->getProductById($id);

        if ($request->getMethod() === 'post') {
            $productModel->delete();
            return $this->back();
        }

        $this->setLayout('admin');
        return $this->render('/admin/products/delete_product', [
            'productModel' => $productModel
        ]);
    }

    /**
     * Method update
     *
     * Updates a product in the database.
     * If the request method is 'post', it loads the data, validates it, and updates it.
     * If the request method is 'get', it renders the 'edit_product' view.
     *
     * @param Request $request The request object containing the request parameters.
     * @return array|bool|string
     */
    public function update(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $productModel = $this->productService->getProductById($id);

        if ($request->getMethod() === 'post') {
            $productModel->loadData($request->getBody());
            $productModel->update();
            return $this->refresh();
        }

        $this->setLayout('admin');
        return $this->render('/admin/products/edit_product', [
            'productModel' => $productModel
        ]);
    }

    /**
     * Method details
     *
     * Fetches the details of a product by its ID and renders the 'details_product' view with the fetched data.
     *
     * @param Request $request The request object containing the request parameters.
     * @return array|bool|string
     */
    public function details(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $productModel = $this->productService->getProductById($id);

        $this->setLayout('admin');
        return $this->render('/admin/products/details_product', [
            'productModel' => $productModel
        ]);
    }

    /**
     * Method product
     *
     * Fetches the product by its ID and renders the 'product_detail' view with the fetched data.
     * If the request method is 'post', it adds the product to the cart.
     *
     * @param Request $request The request object containing the request parameters.
     * @return array|bool|string
     */
    public function product(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $product = $this->productService->getProductById($id);
        $addToCart = false;

        if ($request->getMethod() === 'post') {
            $size = $request->getBody()['size'];
            $note = $request->getBody()['note'];
            $quantity = $request->getBody()['quantity'];
            $cartId = Session::get('cart_id');
            $cartDetail = new CartItem([
                'id' => uniqid(),
                'product_id' => $id,
                'cart_id' => $cartId,
                'quantity' => $quantity,
                'note' => $note,
                'size' => $size
            ]);

            $cartDetail->save();
            $addToCart = true;
        }

        $data = array('product' => $product, 'addToCart' => $addToCart);
        return $this->render('product_detail', $data);
    }
}