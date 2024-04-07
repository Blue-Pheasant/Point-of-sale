<?php

namespace app\Controllers;

use app\Core\Application;
use app\Core\Controller;
use app\Core\Request;
use app\Exception\ForbiddenException;
use app\Middlewares\AuthMiddleware;

use app\Models\LoginForm;
use app\Models\Store;
use app\Models\User;

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
