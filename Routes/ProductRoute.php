<?php

namespace App\Routes;

use App\Core\Route;
use App\Controllers\ProductController;

class ProductRoute extends Route
{
    public function register()
    {
        // User routes
        $this->get('/product', [ProductController::class, 'product']);
        $this->post('/product', [ProductController::class, 'product']);

        // Admin routes
        $this->get('/admin/products', [ProductController::class, 'index']);
        $this->get('/admin/products/delete', [ProductController::class, 'delete']);
        $this->get('/admin/products/edit', [ProductController::class, 'update']);
        $this->get('/admin/products/create', [ProductController::class, 'create']);
        $this->get('/admin/products/details', [ProductController::class, 'details']);

        $this->post('/admin/products/delete', [ProductController::class, 'delete']);
        $this->post('/admin/products/edit', [ProductController::class, 'update']);
        $this->post('/admin/products/create', [ProductController::class, 'create']);
        $this->post('/admin/products/details', [ProductController::class, 'details']);
    }
}