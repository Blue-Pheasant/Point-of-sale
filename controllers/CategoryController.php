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
use app\core\Request;

    class CategoryController extends Controller {
        public function __construct() {}

        public function index() 
        {
            return $this->render('category');    
        }

        public function info(Request $request)
        {
		    $CategoryModel = new Category;
		    if($request->getMethod() === 'post') {

            }
        }

        public function create(Request $request) 
        {
            $CategoryModel = new Category;
            if($request->getMethod() === 'post') {
                $CategoryModel->loadData($request->getBody());
                $CategoryModel->create();
                Application::$app->response->redirect('categories');
            } else if ($request->getMethod() === 'get') {
                $Categories = Category::getAll();
                $this->setLayout('main');
                return $this->render('category', [
                    'model' => $Categories
                ]);
            }
        }

        public function delete(Request $request)
        {
            if($request->getMethod() === 'post') {
                $id = (int)$_REQUEST['id'];
                $CategoryModel = Category::get($id);
                $CategoryModel->delete();
                return Application::$app->response->redirect('categories'); 
            } else if ($request->getMethod() === 'get') {
                $id = (int)$_REQUEST['id'];
                $CategoryModel = Category::get($id);
                $this->setLayout('main');
                return $this->render('category', [
                    'model' => $CategoryModel
                ]);
            }
        }

        public function update(Request $request)
        {
            if($request->getMethod() === 'post') {
                $id = $_REQUEST('id');
                $CategoryModel = Category::get($id);
                $CategoryModel->loadData($request->getBody());
                $CategoryModel->update();
                Application::$app->response->redirect('categories');
            } else if ($request->getMethod() == 'get') {
                $id = (int)$_REQUEST['id'];
                $CategoryModel = Category::get($id); 
                $this->setLayout('main');
                return $this->render('category', [
                    'model' => $CategoryModel
                ]);
            }
        }
    }


