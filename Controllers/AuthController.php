<?php

namespace app\Controllers;

use app\Core\Application;
use app\Core\Controller;
use app\Core\Request;
use app\Core\Response;
use app\Core\Session;
use app\Core\Route;
use app\Exception\ForbiddenException;
use app\Middlewares\AuthMiddleware;
use app\Services\AuthService;
use app\Services\UserService;

use app\Models\LoginForm;
use app\Models\User;

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

                // Set cookie
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
        $this->authService->logout();
        $this->redirect('/');
    }
}