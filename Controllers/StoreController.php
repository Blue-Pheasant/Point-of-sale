<?php
/*
    controllers/store.php
*/

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Store;
use App\Middlewares\AdminMiddleware;

class StoreController extends Controller
{
    public function __construct() 
    {
        $this->registerMiddleware(new AdminMiddleware(['index', 'add', 'delete', 'update']));
    }

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
            return $this->refresh();
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
            return $this->back();
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
            return $this->refresh();
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/stores/edit_store', [
                'storeModel' => $storeModel
            ]);
        } 
    }
}