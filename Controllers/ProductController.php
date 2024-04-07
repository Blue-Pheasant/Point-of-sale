<?php
/*
    controllers/product.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Models\Product;
use app\Core\Application;
use app\Core\Request;
use app\Core\Session;
use app\Models\Cart;
use app\Models\CartItem;
use app\Models\Record;
use app\Services\ProductService;
use app\Middlewares\AdminMiddleware;
use app\Middlewares\AuthMiddleware;

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
                $this->redirect('/admin/products/details_product?id=' . $productModel->getId());
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
            return $this->back();
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
            return $this->refresh();
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