<?php

namespace App\Routes;

use App\Core\Route;
use App\Controllers\UserController;

class UserRoute extends Route
{
    public function register()
    {
        // User routes
        $this->get('/profile', [UserController::class, 'profile']);
        $this->post('/profile', [UserController::class, 'updateProfile']);

        // Admin routes
        $this->get('/admin/users', [UserController::class, 'index']);
        $this->get('/admin/users/delete', [UserController::class, 'delete']);
        $this->get('/admin/users/edit', [UserController::class, 'update']);
        $this->get('/admin/users/create', [UserController::class, 'create']);
        $this->get('/admin/users/details', [UserController::class, 'details']);
        $this->get('/admin/users/edit/password', [UserController::class, 'password']);
        
        $this->post('/admin/users/delete', [UserController::class, 'delete']);
        $this->post('/admin/users/edit', [UserController::class, 'update']);
        $this->post('/admin/users/create', [UserController::class, 'create']);
        $this->post('/admin/users/details', [UserController::class, 'details']);
        $this->post('/admin/users/edit/password', [UserController::class, 'password']);
    }
}