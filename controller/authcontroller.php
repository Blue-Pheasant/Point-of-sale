<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->setLayout('auth');
        if ($request->isPost()) {
            return "Handle submit data ...";
        }
        return $this->render('login');
    }

    public function register(Request $request)
    {
        $this->setLayout('auth');
        $user = new User();
        if ($request->isPost()) {

            $user->loadData($request->getBody());

            if ($user->validate() && $user->save()) {
                var_dump("here");
                Application::$app->session->setFlash('success', 'Thanks for registering');
                Application::$app->response->redirect("/");
                exit;
            }
            return $this->render('register', [
                'model' => $user
            ]);
        }
        return $this->render('register', [
            'model' => $user
        ]);
    }
}