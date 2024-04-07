<?php

namespace app\Routes;

use app\Core\Route;
use app\Controllers\StoreController;
use app\Controllers\SiteController;
use app\Controllers\MenuController;
use app\Controllers\CartController;

class CustomerRoute extends Route
{
    public function register()
    {
        // Customer routes
        $this->get('/', [SiteController::class, 'home']);
        $this->get('/contact', [SiteController::class, 'contact']);
        $this->get('/about', [SiteController::class, 'about']);
        $this->get('/menu', [MenuController::class, 'menu']);
        $this->get('/stores', [SiteController::class, 'stores']);
        $this->get('/cart/notice', [SiteController::class, 'notice']);
        $this->get('/cart', [CartController::class, 'cart']);
        $this->get('/error', [SiteController::class, 'error']);

        $this->post('/menu', [MenuController::class, 'search']);
        $this->post('/cart', [CartController::class, 'placeOrder']);
        $this->post('/update', [CartController::class, 'update']);
    }
}