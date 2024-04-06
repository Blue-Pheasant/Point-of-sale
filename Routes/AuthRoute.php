<?php

namespace App\Routes;

use App\Core\Route;
use App\Controllers\AuthController;

class AuthRoute extends Route
{
    public function register()
    {
        // User routes
        $this->get('/login', [AuthController::class, 'login']);
        $this->post('/login', [AuthController::class, 'login']);
        $this->get('/logout', [AuthController::class, 'logout']);

        $this->get('/register', [AuthController::class, 'register']);
        $this->post('/register', [AuthController::class, 'register']);
    }
}