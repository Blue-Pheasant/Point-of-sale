<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Route;
use App\Exception\ForbiddenException;
use App\Middlewares\AuthMiddleware;
use App\Services\AuthService;
use App\Services\UserService;

use App\Models\LoginForm;
use App\Models\User;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected UserService $userService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
        $this->authService->loginWithCookie();
    }

    public function login(Request $request)
    {
        $loginForm = new LoginForm();
        if ($request->getMethod() === 'post') {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login('email')) {
                $userId = Session::get('user');
                $userModel = $this->userService->getUserById($userId);
                setcookie("member_login", $userId, time() + 3600 * 24 * 30);

                if ($userModel->getRole() === 'admin') {
                    return $this->intended('/admin');
                } else {
                    return $this->intended('/');
                }
            }
        }
        $this->setLayout('auth');
        return $this->render('login', [
            'model' => $loginForm
        ]);
    }

    public function register(Request $request)
    {
        $registerModel = new User([]);
        if ($request->getMethod() === 'post') {
            $registerModel->loadData($request->getBody());
            if ($registerModel->validate() && $registerModel->save()) {
                Session::setFlash('success', 'Thanks for registering');
                $this->redirect('/');
            }
        }
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $registerModel
        ]);
    }

    public function logout()
    {
        Application::$app->logout();
        $this->redirect('/');
    }
}