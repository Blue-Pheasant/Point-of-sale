<?php
/*
    controllers/product.php
*/

namespace app\controllers;

use app\core\Controller;
use app\models\Product;
use app\core\Application;
use app\core\Request;
use app\core\Session;
use app\models\Cart;
use app\models\CartItem;
use app\models\Record;
use app\Services\ProductService;
use app\middlewares\AdminMiddleware;
use app\middlewares\AuthMiddleware;

class ProductController extends Controller
{
    protected ProductService $productService;
    public function __construct()
    {
        $this->productService = new ProductService();
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'create', 'delete', 'update', 'details']);
        $this->registerMiddleware(AuthMiddleware::class, ['product']);
    }

    public function index()
    {
        $products = $this->productService->getAllProducts(['limit' => 10, 'page' => 1])['list'];
        $this->setLayout('admin');
        return $this->render('/admin/products/products', [
            'products' => $products
        ]);
    }

    public function create(Request $request)
    {
        $productModel = new Product();
        if ($request->getMethod() === 'post') {
            $productModel->loadData($request->getBody());
            if ($productModel->validate() && $productModel->save()) {
                $this->setFlash('success', 'Create product successuly');
                $this->redirect('/admin/products');
            } else {
                $this->setFlash('fail', 'Create product fail');
            }
        } else if ($request->getMethod() === 'get') {
            $products = Product::getAllProducts();
            $this->setLayout('admin');
            return $this->render('/admin/products/create_product', [
                'productModel' => $products
            ]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->getParam('id');
        $productModel = $this->productService->getProductById($id);
        if ($request->getMethod() === 'post') {
            $productModel->delete();
            return $this->redirect('/admin/products');
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/products/delete_product', [
                'productModel' => $productModel
            ]);
        }
    }

    public function update(Request $request)
    {
        $id = Application::$app->request->getParam('id');
        $productModel = $this->productService->getProductById($id);
        if ($request->getMethod() === 'post') {
            $productModel->loadData($request->getBody());
            $productModel->update($productModel);
            Application::$app->response->redirect('/admin/products');
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/products/edit_product', [
                'productModel' => $productModel
            ]);
        }
    }

    public function details(Request $request)
    {
        $id = $request->getParam('id');
        $productModel = $this->productService->getProductById($id);
        $this->setLayout('admin');
        return $this->render('/admin/products/details_product', [
            'productModel' => $productModel
        ]);
    }

    public function product(Request $request)
    {
        $id = $request->getParam('id');
        $product = $this->productService->getProductById($id);
        $addToCart = false;

        if ($request->getMethod() === 'post') {
            $size = $request->getBody()['size'];
            $note = $request->getBody()['note'];
            $quantity = $request->getBody()['quantity'];
            $cart_id = Application::$app->cart->id;
            $cartDetail = new CartItem([
                'id' => uniqid(),
                'product_id' => $id,
                'cart_id' => $cart_id,
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