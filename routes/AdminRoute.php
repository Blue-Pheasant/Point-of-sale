<?php

namespace app\routes;

use app\core\Route;
use app\controllers\AdminController;

class AdminRoute extends Route
{
    public function register()
    {
        // Admin routes
        $this->get('/admin', [AdminController::class, 'index']);
        $this->get('/admin/profile', [AdminController::class, 'profile']);
        $this->post('/admin/profile', [AdminController::class, 'profile']);
    }
}