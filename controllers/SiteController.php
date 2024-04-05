<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\exception\ForbiddenException;
use app\middlewares\AuthMiddleware;

use app\models\LoginForm;
use app\models\Store;
use app\models\User;

class SiteController extends Controller
{
    public function home()
    {
        return $this->render('home', [
            'name' => 'Buy me store'
        ]);
    }

    public function error() {
        $this->setLayout('auth');
        return $this->render('permission');
    }

    public function about()
    {
        return $this->render('about');
    }

    public function stores()
    {
        $stores = Store::getAll();
        $this->setLayout('main');
        return $this->render('stores', [
            'store' => $stores
        ]);
    }

    public function contact()
    {
        return $this->render('contact');
    }

    public function notice()
    {
        $this->setLayout('auth');
        return $this->render('payment_success');
    }
}
