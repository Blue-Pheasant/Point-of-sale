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
            $this->redirect('/admin/stores');
        } else if($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/stores/create_store',  [
                'storeModel' => $storeModel
            ]);
        }
    }

    public function details(Request $request)
    {
        $storeId = $request->getParam('id');
        $storeModel = Store::get($storeId);
        $this->setLayout('admin');
        return $this->render('/admin/stores/details_store', [
            'model' => $storeModel
        ]);
    }

    public function delete(Request $request)
    {
        $id = $request->getParam('id');
        $storeModel = Store::get($id);
        if ($request->getMethod() === 'post') {
            $storeModel->delete();
            return $this->redirect('/admin/stores');
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/stores/delete_store', [
                'storeModel' => $storeModel
            ]);
        }
    }

    public function update(Request $request)
    {
        $id = $request->getParam('id');
        $storeModel = Store::get($id);
        if ($request->getMethod() === 'post') {
            $storeModel->loadData($request->getBody());
            $storeModel->update($storeModel);
            $this->redirect('/admin/stores');
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/stores/edit_store', [
                'storeModel' => $storeModel
            ]);
        } 
    }
}