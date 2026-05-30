<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Controllers\Api\ProductController;
use app\Middlewares\ApiTokenMiddleware;
use app\Routes\ApiRoute;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see ApiRoute} (roadmap T23): the API routes are registered
 * under the `/api` prefix, map to the API controllers, and every one is guarded
 * by {@see ApiTokenMiddleware}.
 */
final class ApiRouteTest extends TestCase
{
    /** @return array<string, array{path: string, callback: mixed, middleware: array<int, mixed>}> */
    private function getRoutes(): array
    {
        $routes = (new ApiRoute())->routes();
        $byPath = [];
        foreach ($routes['get'] ?? [] as $route) {
            $byPath[$route['path']] = $route;
        }

        return $byPath;
    }

    public function testRegistersProductEndpointsUnderApiPrefix(): void
    {
        $routes = $this->getRoutes();

        $this->assertArrayHasKey('/api/products', $routes);
        $this->assertArrayHasKey('/api/products/{id}', $routes);
    }

    public function testEndpointsMapToTheApiProductController(): void
    {
        $routes = $this->getRoutes();

        $this->assertSame([ProductController::class, 'index'], $routes['/api/products']['callback']);
        $this->assertSame([ProductController::class, 'show'], $routes['/api/products/{id}']['callback']);
    }

    public function testEveryApiRouteIsTokenGuarded(): void
    {
        foreach ($this->getRoutes() as $path => $route) {
            $middlewareClasses = array_map(static fn ($mw) => $mw[0], $route['middleware']);
            $this->assertContains(
                ApiTokenMiddleware::class,
                $middlewareClasses,
                "Route $path must be guarded by the API token middleware."
            );
        }
    }
}
