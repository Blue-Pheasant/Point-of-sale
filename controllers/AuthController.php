<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\core\Session;
use app\core\Route;
use app\exception\ForbiddenException;
use app\middlewares\AuthMiddleware;
use app\services\AuthService;
use app\services\UserService;

use app\models\LoginForm;
use app\models\User;

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