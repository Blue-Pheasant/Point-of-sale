<?php
/*
    controllers/categories/index.php
*/
namespace app\controllers;

use app\core\Controller;
use app\models\Category;
use app\core\Application;
use app\core\Request;
use app\core\Response;
use app\services\CategoryService;
use app\middlewares\AdminMiddleware;

class CategoryController extends Controller {
    private CategoryService $categoryService;

    public function __construct() 
    {
        $this->categoryService = new CategoryService();
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'create', 'delete', 'update', 'details']);
    }

    public function index() 
    {
        $categories = $this->categoryService->getAllCategories();
        $this->setLayout('admin');
        return $this->render('/admin/categories/categories', [
            'category' => $categories
        ]);      
    }

    public function details(Request $request)
    {
        if($request->getMethod() === 'get') {
            $id = $request->getParam('id');
            $categoryModel = $this->categoryService->getCategoryById($id);
            $this->setLayout('admin');
            return $this->render('/admin/categories/details_category', [
                'model' => $categoryModel
            ]);
        }
    }

    public function create(Request $request) 
    {
        $categoryModel = new Category();
        if($request->getMethod() === 'post') {
            $categoryModel->loadData($request->getBody());
            $categoryModel->save();
            $this->redirect('/admin/categories/details_category?id=' . $categoryModel->getId());
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/categories/create_category', [
                'model' => $categoryModel
            ]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->getParam('id');;
        $categoryModel = $this->categoryService->getCategoryById($id);
        if($request->getMethod() === 'post') {
            $categoryModel->delete();
            return $this->back(); 
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/categories/delete_category', [
                'model' => $categoryModel
            ]);
        }
    }

    public function update(Request $request)
    {
        $id = $request->getParam('id');
        $categoryModel = $this->categoryService->getCategoryById($id);
        if ($request->getMethod() === 'post') {
            $categoryModel->loadData($request->getBody());
            $categoryModel->update($categoryModel);
            $this->refresh();
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/categories/edit_category', [
                'categoryModel' => $categoryModel
            ]);
        }
    }
}
