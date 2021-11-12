<?php
/*
    controllers/user.php
*/
namespace app\controllers;

use app\core\Controller;
use app\core\Input;
use app\core\Application;
use app\core\Request;
use app\core\Session;
use app\models\User;

class UserController extends Controller{
    public function __construct() {}

    public function index() 
    {
        return $this->render('user');
    }

    public function delete(Request $request)
    {
        if($request->getMethod() === 'post') {
            $id = $_REQUEST('id');
            $userModel = User::get($id);
            $userModel->delete();
            return Application::$app->response->redirect('products');
        } else if($request->getMethod() === 'get') {
            $id = (int)$_REQUEST['id'];
            $userModel = User::get($id);
            $this->setLayout('main');
            return $this->render('user', [
                'model' => $userModel
            ]);
        }        
    }

    public function update(Request $request)
    {
        if($request->getMethod() === 'post') {
            $id = $_REQUEST('id');
            $userModel = User::get($id);
            $userModel->loadData($request->getBody());
            $userModel->update();
            Application::$app->response->redirect('products');
        } else if ($request->getMethod() == 'get') {
            $id = (int)$_REQUEST['id'];
            $userModel = User::get($id);
            $this->setLayout('main');
            return $this->render('user', [
                'model' => $userModel
            ]);
        }        
    }

    public function view(Request $request)
    {
        if($request->getMethod() === 'p')
        $id = (int)$_REQUEST['id'];
        $userModel = User::get($id);
        $this->setLayout('main');
        return $this->render('user', [
            'model' => $userModel
        ]);         
    }
}