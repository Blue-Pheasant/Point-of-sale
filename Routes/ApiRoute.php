<?php

namespace app\Routes;

use app\Controllers\Api\ProductController;
use app\Core\Route;
use app\Middlewares\ApiTokenMiddleware;

/**
 * JSON REST API routes (roadmap T23).
 *
 * Grouped under the `/api` prefix and guarded by {@see ApiTokenMiddleware} so
 * every endpoint requires a bearer token (auth decoupled from the session
 * cookie). The `/api/*` prefix is exempt from the session CSRF check (see
 * {@see \app\Middlewares\CsrfMiddleware}).
 */
class ApiRoute extends Route
{
    public function register()
    {
        $this->group([
            'prefix'     => '/api',
            'middleware' => [ApiTokenMiddleware::class],
        ], function (Route $route): void {
            $route->get('/products', [ProductController::class, 'index']);
            $route->get('/products/{id}', [ProductController::class, 'show']);
        });
    }
}
