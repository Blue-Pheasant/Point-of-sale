<?php

namespace app\routes;

use app\core\Route;
use app\controllers\OrderController;

class OrderRoute extends Route
{
    public function register()
    {
        // User routes
        $this->get('/orders', [OrderController::class, 'orders']);
        $this->get('/order', [OrderController::class, 'orderDetail']);
        $this->post('/orders/clear', [OrderController::class, 'clear']);

        // Admin routes
        $this->get('/admin/orders', [OrderController::class, 'index']);
        $this->get('/admin/orders/accept', [OrderController::class, 'accept']);
        $this->get('/admin/orders/reject', [OrderController::class, 'reject']);
        $this->get('/admin/orders/details', [OrderController::class, 'orderDetails']);
        $this->get('/admin/orders/accepted', [OrderController::class, 'accepted']);
        $this->get('/admin/orders/rejected', [OrderController::class, 'rejected']);
        $this->get('/admin/orders/rejected/delete', [OrderController::class, 'delete']);
        $this->get('/admin/orders/rejected/details', [OrderController::class, 'details']);
        $this->get('/admin/orders/accepted/delete', [OrderController::class, 'delete']);
        $this->get('/admin/orders/accepted/details', [OrderController::class, 'details']);
        
        $this->post('/admin/orders/accepted', [OrderController::class, 'accepted']);
        $this->post('/admin/orders/rejected', [OrderController::class, 'rejected']);
    }
}