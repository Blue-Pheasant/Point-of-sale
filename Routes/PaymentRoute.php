<?php

namespace app\Routes;

use app\Controllers\PaymentController;
use app\Core\Route;

/**
 * Payment routes (roadmap T22).
 *
 * `pay` starts a payment (state-changing → POST). The gateway callback is
 * accepted on both POST (server-to-server notification) and GET (browser
 * return redirect); it is authenticated by the gateway's HMAC signature, not
 * the session CSRF token (see {@see \app\Middlewares\CsrfMiddleware}).
 */
class PaymentRoute extends Route
{
    public function register()
    {
        $this->post('/payment/pay', [PaymentController::class, 'pay']);

        $this->post('/payment/callback', [PaymentController::class, 'callback']);
        $this->get('/payment/callback', [PaymentController::class, 'callback']);
    }
}
