<?php
/*
    controllers/category/index.php
*/
namespace app\controllers;
namespace app\models;
use app\core\Controller;
use app\core\Input;
use app\core\Response;
use app\core\Session;
use app\models\Cart;
use app\core\Application;
use app\core\CartSession;

class CartController extends Controller {
        public function __construct() {}

        public function index() 
        {
            $cart = CartSession::Get();
            $model = $cart->products;
            $this->setLayout('main');
            return $this->render('cart', [
                'model' => $cart
            ]);   
        }

        public function info()
        {
            
        }

        public function remove()
        {
            
        }

        public function empty()
        {
            CartSession::Remove();
            Application::$app->response->redirect('products');
        }

        public function checkout()
        {
            $cart = CartSession::Get();
            $model = $cart->products;
            $this->setLayout('main');
            return $this->render('checkout', [
                'model' => $model
            ]);
        }

        public function ConfirmCheckout()
        {
            if (CartSession::Exists()) {
                $cart = CartSession::Get();
                $cart->products;
                foreach ($cart->products as $product) {
                    
                    if(!Application::$app->isGuest()) {
                        Application::$app->response->redirect('login');
                    }
                    foreach($cart->pruducts as $product) {
                        $record = new Record(
                            $userId,
                            $product->getId(),
                            $quantity,
                            $totalPrice,
                            $saleDate = date("Y-m-d" . " H:i:s",time() + 7 * 3600)
                        );
                        $record->create();
                    }
                }
                Application::$app->response->redirect('cart', 'Empty'); // Succesful, redirect to sale history
            } else {
                Application::$app->response->redirect('products');
            }
        }
    }


