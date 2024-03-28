<?php
/*
    controllers/store.php
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\Store;

class StoreController extends Controller
{
    public function __construct() {}

    public function index()
    {
        $stores = Store::getAll();
        $this->setLayout('admin');
        return $this->render('/admin/stores/stores', [
            'store' => $stores
        ]);
    }

    public function add(Request $request)
    {
        $storeModel = new Store;
        if($request->getMethod() === 'post') {
            $storeModel->loadData($request->getBody());
            $storeModel->save();
            Application::$app->response->redirect('/admin/stores');
        } else if($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/stores/create_store',  [
                'storeModel' => $storeModel
            ]);
        }
    }

    public function delete(Request $request)
    {
        if ($request->getMethod() === 'post') {
            $id = Application::$app->request->getParam('id');
            $storeModel = Store::get($id);
            $storeModel->delete();
            return Application::$app->response->redirect('/admin/stores');
        } else if ($request->getMethod() === 'get') {
            $id = Application::$app->request->getParam('id');
            $storeModel = Store::get($id);
            $this->setLayout('admin');
            return $this->render('/admin/stores/delete_store', [
                'storeModel' => $storeModel
            ]);
        }
    }

    public function update(Request $request)
    {
        if ($request->getMethod() === 'post') {
            $id = Application::$app->request->getParam('id');
            $storeModel = Store::get($id);
            $storeModel->loadData($request->getBody());
            $storeModel->update($storeModel);
            Application::$app->response->redirect('/admin/stores');
        } else if ($request->getMethod() === 'get') {
            $id = Application::$app->request->getParam('id');
            $storeModel = Store::get($id);
            $this->setLayout('admin');
            return $this->render('/admin/stores/edit_store', [
                'storeModel' => $storeModel
            ]);
        } 
    }
}