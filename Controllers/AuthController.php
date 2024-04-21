<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Request;
use app\Core\Session;
use app\Services\AuthService;
use app\Services\UserService;
use app\Models\LoginForm;
use app\Models\User;

/**
 * Class AuthController
 *
 * This class is responsible for handling the authentication operations of the application.
 * It extends the base Controller class and uses services for authentication and users.
 *
 * @package app\Controllers
 */
class AuthController extends Controller
{
    /**
     * @var AuthService $authService An instance of AuthService to handle authentication-related operations.
     */
    protected AuthService $authService;

    /**
     * @var UserService $userService An instance of UserService to handle user-related operations.
     */
    protected UserService $userService;

    /**
     * AuthController constructor.
     *
     * Initializes the services and attempts to log in with cookie.
     */
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
        $this->authService->loginWithCookie();
    }

    /**
     * Method login
     *
     * Handles the login operation for the user.
     * If the request method is 'post', it loads the request data into the login form model,
     * validates the data, and if valid, logs in the user.
     * Sets the layout to 'auth' and renders the 'login' view with the login form model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function login(Request $request): array|bool|string
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

    /**
     * Method register
     *
     * Handles the registration operation for the user.
     * If the request method is 'post', it loads the request data into the user model,
     * validates the data, and if valid, saves the user.
     * Sets the layout to 'auth' and renders the 'register' view with the user model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function register(Request $request): array|bool|string
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

    /**
     * Method logout
     *
     * Handles the logout operation for the user.
     * Redirects the user to the home page after logging out.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/');
    }
}