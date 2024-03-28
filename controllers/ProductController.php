<?php
/*
    controllers/product.php
*/

namespace app\controllers;

use app\core\Controller;
use app\models\Product;
use app\core\Application;
use app\core\Request;
use app\models\Cart;
use app\models\CartDetail;
use app\models\Record;

class ProductController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $products = Product::getAllProducts();
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
            $productModel->save();
            Application::$app->response->redirect('/admin/products');
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
        if ($request->getMethod() === 'post') {
            $id = Application::$app->request->getParam('id');
            $productModel = Product::getProductDetail($id);
            $productModel->delete();
            return Application::$app->response->redirect('/admin/products');
        } else if ($request->getMethod() === 'get') {
            $id = Application::$app->request->getParam('id');
            $productModel = Product::getProductDetail($id);
            $this->setLayout('admin');
            return $this->render('/admin/products/delete_product', [
                'productModel' => $productModel
            ]);
        }
    }


    public function update(Request $request)
    {
        if ($request->getMethod() === 'post') {
            $id = Application::$app->request->getParam('id');
            $productModel = Product::getProductDetail($id);
            $productModel->loadData($request->getBody());
            $productModel->update($productModel);
            Application::$app->response->redirect('/admin/products');
        } else if ($request->getMethod() === 'get') {
            $id = Application::$app->request->getParam('id');
            $productModel = Product::getProductDetail($id);
            $this->setLayout('admin');
            return $this->render('/admin/products/edit_product', [
                'productModel' => $productModel
            ]);
        }
    }

    public function details(Request $request)
    {
        if ($request->getMethod() === 'get') {
            $id = Application::$app->request->getParam('id');
            $productModel = Product::getProductDetail($id);
            $this->setLayout('admin');
            return $this->render('/admin/products/details_product', [
                'productModel' => $productModel
            ]);
        }
    }

    public function product(Request $request)
    {
        $product_id = Application::$app->request->getParam('id');
        $product = Product::getProductDetail($product_id);


        $addToCart = false;

        if ($request->getMethod() === 'post') {
            $size = $request->getBody()['size'];
            $note = $request->getBody()['note'];
            $quantity = $request->getBody()['quantity'];
            $cart_id = Application::$app->cart->id;
            $cartDetail = new CartDetail(
                uniqid(),
                $product_id,
                $cart_id,
                $quantity,
                $note,
                $size
            );
            $cartDetail->save();

            $addToCart = true;
        }

        $data = array('product' => $product, 'addToCart' => $addToCart);
        return $this->render('product_detail', $data);
    }
}