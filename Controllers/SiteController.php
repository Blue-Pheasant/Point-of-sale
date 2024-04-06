<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Exception\ForbiddenException;
use App\Middlewares\AuthMiddleware;

use App\Models\LoginForm;
use App\Models\Store;
use App\Models\User;

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
