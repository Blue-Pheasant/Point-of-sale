<?php

namespace app\Routes;

use app\Controllers\AdminController;
use app\Core\Route;

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
