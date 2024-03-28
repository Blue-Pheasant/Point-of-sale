<?php
/*
    controllers/categories/index.php
*/
namespace app\controllers;

use app\core\Controller;
use app\models\Category;
use app\core\Application;
use app\core\Request;

class CategoryController extends Controller {
    public function __construct() {}

        public function index() 
        {
            $models = Category::getAllCategories();
            $this->setLayout('admin');
            return $this->render('/admin/categories/categories', [
                'category' => $models
            ]);      
        }

        public function details(Request $request)
        {
            if($request->getMethod() === 'get') {
                $id = Application::$app->request->getParam('id');
                $categoryModel = Category::get($id);
                $this->setLayout('admin');
                return $this->render('/admin/categories/details_category', [
                    'model' => $categoryModel
                ]);
            }
        }

        public function create(Request $request) 
        {
            $categoryModel = new Category;
            if($request->getMethod() === 'post') {
                $categoryModel->loadData($request->getBody());
                $categoryModel->save();
                Application::$app->response->redirect('/admin/categories');
            } else if ($request->getMethod() === 'get') {
                $this->setLayout('admin');
                return $this->render('/admin/categories/create_category', [
                    'model' => $categoryModel
                ]);
            }
        }

        public function delete(Request $request)
        {
            if($request->getMethod() === 'post') {
                $id = Application::$app->request->getParam('id');;
                $categoryModel = Category::get($id);
                $categoryModel->delete();
                return Application::$app->response->redirect('/admin/categories'); 
            } else if ($request->getMethod() === 'get') {
                $id = Application::$app->request->getParam('id');;
                $categoryModel = Category::get($id);
                $this->setLayout('admin');
                return $this->render('/admin/categories/delete_category', [
                    'model' => $categoryModel
                ]);
            }
        }

        public function update(Request $request)
        {
            if ($request->getMethod() === 'post') {
                $id = Application::$app->request->getParam('id');
                $categoryModel = Category::get($id);
                $categoryModel->loadData($request->getBody());
                $categoryModel->update($categoryModel);
                Application::$app->response->redirect('/admin/categories');
            } else if ($request->getMethod() === 'get') {
                $id = Application::$app->request->getParam('id');
                $categoryModel = Category::get($id);
                $this->setLayout('admin');
                return $this->render('/admin/categories/edit_category', [
                    'categoryModel' => $categoryModel
                ]);
            }
        }
}
