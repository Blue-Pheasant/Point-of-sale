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
use app\models\Category;
use app\core\Application;
use app\core\UserModel;

    class CategoryController extends Controller {
        public function __construct() {}

        public function index() 
        {
            return $this->render('category');    
        }

        public function info()
        {
            $category_id = Input::get('category_id');
		    $CategoryModel = new Category;
		    $category_infor = $CategoryModel->findById($category_id);
		    return $this->jsonResponse($category_infor);
        }

        public function add() 
        {
            $CategoryModel = new Category;
            $CategoryModel->id = uniqid();
            $CategoryModel->category_name = Input::get('category_name'); 
            $CategoryModel->create_at = date("d-m-Y",time());
            $CategoryModel->validate();
            if ($CategoryModel->validate() && $CategoryModel->save()) {
                Application::$app->session->setFlash('Success', 'System`s added new product');
                Application::$app->response->redirect('product');
            }   
        }

        public function remove()
        {
            $category_id = Input::get('category_id');
            $CategoryModel = new Category;
            if($CategoryModel->delete($category_id)) {
                Session::set('Success', 'Product has id ' . $category_id . ' has been deleted.');
                Response::redirect('category');
            }
        }

        public function update()
        {
            
        }
    }


