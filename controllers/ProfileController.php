<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\middlewares\AuthMiddleware;
use app\models\User;

class ProfileController extends Controller
{
    public function __construct()
    {   
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }
    
    public function profile(Request $request)
    {
        $updateSuccess = false;
        $id = Application::$app->user->id;
        $user = User::getUserInfo($id);
        if ($request->getMethod() === 'post') {
            $user->loadData($request->getBody());
            if ($user->validateUpdateProfile() && true) {
                if ($user->updateProfile($user)) {
                    $updateSuccess = true;
                }
            }
        }

        $user = User::getUserInfo($id);
        Application::$app->user = $user;

        return $this->render('profile', [
            'user' => $user,
            'updateSuccess' => $updateSuccess,
        ]);
    }
}