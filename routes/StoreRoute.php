<?php

namespace app\routes;

use app\core\Route;
use app\controllers\StoreController;

class StoreRoute extends Route
{
    public function register()
    {
        // User routes
        $this->get('/admin/stores', [StoreController::class, 'index']);

        // Admin routes
        $this->get('/admin/stores/delete', [StoreController::class, 'delete']);
        $this->get('/admin/stores/edit', [StoreController::class, 'update']);
        $this->get('/admin/stores/add', [StoreController::class, 'add']);
        $this->get('/admin/stores/details', [StoreController::class, 'details']);
        
        $this->post('/admin/stores/delete', [StoreController::class, 'delete']);
        $this->post('/admin/stores/edit', [StoreController::class, 'update']);
        $this->post('/admin/stores/add', [StoreController::class, 'add']);
        $this->post('/admin/stores/details', [StoreController::class, 'details']);
    }
}