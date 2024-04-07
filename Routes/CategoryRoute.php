<?php

namespace app\Routes;

use app\Core\Route;
use app\Controllers\CategoryController;

class CategoryRoute extends Route
{
    public function register()
    {
        // Admin routes
        $this->get('/admin/categories', [CategoryController::class, 'index']);
        $this->get('/admin/categories/delete', [CategoryController::class, 'delete']);
        $this->get('/admin/categories/edit', [CategoryController::class, 'update']);
        $this->get('/admin/categories/create', [CategoryController::class, 'create']);
        $this->get('/admin/categories/details', [CategoryController::class, 'details']);

        $this->post('/admin/categories/delete', [CategoryController::class, 'delete']);
        $this->post('/admin/categories/edit', [CategoryController::class, 'update']);
        $this->post('/admin/categories/create', [CategoryController::class, 'create']);
        $this->post('/admin/categories/details', [CategoryController::class, 'details']);
    }
}