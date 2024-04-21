<?php
/*
    controllers/store.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Request;
use app\Models\Store;
use app\Middlewares\AdminMiddleware;

/**
 * Class StoreController
 *
 * This class is responsible for handling the store operations of the application.
 * It extends the base Controller class and uses the Store model.
 * It also uses middleware for administrative tasks.
 *
 * @package app\Controllers
 */
class StoreController extends Controller
{
    /**
     * StoreController constructor.
     *
     * Registers the middleware.
     */
    public function __construct() 
    {
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'add', 'delete', 'update']);
    }

    /**
     * Method index
     *
     * Fetches all stores and renders the 'stores' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function index(): array|bool|string
    {
        $stores = Store::getAll();
        $this->setLayout('admin');
        return $this->render('/admin/stores/stores', [
            'store' => $stores
        ]);
    }

    /**
     * Method add
     *
     * Adds a new store to the database.
     *
     * @param Request $request
     * @return array|bool|string
     */
    public function add(Request $request): array|bool|string
    {
        $storeModel = new Store;
        if($request->getMethod() === 'post') {
            $storeModel->loadData($request->getBody());
            $storeModel->save();
            return $this->refresh();
        }

        $this->setLayout('admin');
        return $this->render('/admin/stores/create_store',  [
            'storeModel' => $storeModel
        ]);
    }

    /**
     * Method details
     *
     * Fetches the store by ID and renders the 'details_store' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function details(Request $request): array|bool|string
    {
        $storeId = $request->getParam('id');
        $storeModel = Store::get($storeId);
        
        $this->setLayout('admin');
        return $this->render('/admin/stores/details_store', [
            'model' => $storeModel
        ]);
    }

    /**
     * Method delete
     *
     * Deletes a store by ID.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function delete(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $storeModel = Store::get($id);
        if ($request->getMethod() === 'post') {
            $storeModel->delete();
            return $this->back();
        }

        $this->setLayout('admin');
        return $this->render('/admin/stores/delete_store', [
            'storeModel' => $storeModel
        ]);
    }

    /**
     * Method update
     *
     * Updates a store by ID.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function update(Request $request): array|bool|string
    {
        $id = $request->getParam('id');
        $storeModel = Store::get($id);
        if ($request->getMethod() === 'post') {
            $storeModel->loadData($request->getBody());
            $storeModel->update();
            return $this->refresh();
        }

        $this->setLayout('admin');
        return $this->render('/admin/stores/edit_store', [
            'storeModel' => $storeModel
        ]);
    }
}