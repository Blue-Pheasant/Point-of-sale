<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
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
        $this->authService->loginWithCookie();
        $this->registerMiddleware(new AuthMiddleware(['profile']));
        $this->userService = new UserService();
    }

    public function login(Request $request)
    {
        $loginForm = new LoginForm();
        if ($request->getMethod() === 'post') {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login('email')) {
                $userId = Application::$app->session->get('user');
                $userModel = $this->userService->getUserById($userId);
                setcookie("member_login", $userId, time() + 3600 * 24 * 30);

                if ($userModel->getRole() === 'admin') {
                    return Application::$app->router->intended('/admin');
                } else {
                    return Application::$app->router->intended('/');
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
                Application::$app->session->setFlash('success', 'Thanks for registering');
                Application::$app->response->redirect('/');
                return 'Show success page';
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
        Application::$app->response->redirect('/');
    }
}