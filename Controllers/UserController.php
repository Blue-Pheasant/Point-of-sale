<?php
/*
    controllers/user.php
*/
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Application;
use App\Core\Request;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;
    
    public function __construct() 
    {
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'create', 'delete', 'update', 'details']);
        $this->registerMiddleware(AuthMiddleware::class, ['profile', 'updateProfile', 'password']);
        $this->userService = new UserService();
    }

    public function index() 
    {
        $users = User::getAllUsers();
        $this->setLayout('admin');
        return $this->render('/admin/users/users', [
            'users' => $users
        ]);
    }

    public function create(Request $request)
    {
        $userModel = new User();
        if($request->getMethod() === 'post') {
            $userModel->loadData($request->getBody());
            if($userModel->getRole() === 'client') {
                $userModel->saveAdmin($userModel->getRole());
            } else {
                $userModel->save();
            }
            return $this->refresh();
        } else if($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/users/create_user',  [
                'userModel' => $userModel
            ]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);
        if($request->getMethod() === 'post') {
            $userModel->delete();
            return $this->back();
        } else if($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/users/delete_user', [
                'userModel' => $userModel
            ]);
        }        
    }

    public function update(Request $request)
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);
        if($request->getMethod() === 'post') {
            $userModel->loadData($request->getBody());
            $userModel->update($userModel);
            return $this->refresh();
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/users/edit_user', [
                'userModel' => $userModel
            ]);
        }        
    }

    public function details(Request $request)
    {
        if($request->getMethod() === 'get')
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);
        $this->setLayout('admin');
        return $this->render('/admin/users/details_user', [
            'userModel' => $userModel
        ]);         
    }

    public function password(Request $request)
    {
        $id = $request->getParam('id');
        $userModel = $this->userService->getUserById($id);
        if($request->getMethod() === 'post') {
            $userModel->loadData($request->getBody());
            $userModel->update($userModel);
            return $this->refresh();
        } else if ($request->getMethod() === 'get') {
            $this->setLayout('admin');
            return $this->render('/admin/users/change_password', [
                'userModel' => $userModel
            ]);
        }        
    }

    public function profile()
    {
        return $this->render('profile');
    }

    public function updateProfile(Request $request)
    {
        $updateSuccess = false;
        $id = Application::$app->user->id;
        $user = $this->userService->getUserById($id);
        if ($request->getMethod() === 'post') {
            $user->loadData($request->getBody());
            if ($user->validateUpdateProfile() && true) {
                if ($user->updateProfile($user)) {
                    $updateSuccess = true;
                }
            }
        }

        $user = $this->userService->getUserById($id);
        Application::$app->user = $user;

        return $this->render('profile', [
            'user' => $user,
            'updateSuccess' => $updateSuccess,
        ]);
    }
}