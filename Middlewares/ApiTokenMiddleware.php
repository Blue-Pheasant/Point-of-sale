<?php

namespace app\Middlewares;

use app\Core\Application;
use app\Core\Middleware;

/**
 * Authenticates JSON API requests with a bearer token (roadmap T23).
 *
 * API auth is intentionally decoupled from the session cookie: a client sends
 * `Authorization: Bearer <token>` (or an `X-Api-Token` header), which is
 * compared in constant time against the `API_TOKEN` environment value. A
 * missing or wrong token yields a JSON 401 — never an HTML error page — so API
 * consumers always get a machine-readable response.
 *
 * @package app\Middlewares
 */
class ApiTokenMiddleware extends Middleware
{
    public function execute(): void
    {
        $expected = (string) ($_ENV['API_TOKEN'] ?? '');
        $provided = $this->bearerToken();

        if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
            Application::$app->response->json([
                'error' => ['message' => 'Unauthorized.'],
            ], 401);
        }
    }

    /**
     * Extracts the bearer token from the `Authorization` header, falling back
     * to an `X-Api-Token` header.
     */
    private function bearerToken(): string
    {
        $header = (string) (
            $_SERVER['HTTP_AUTHORIZATION']
            ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '')
        );

        if (preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches) === 1) {
            return trim($matches[1]);
        }

        return trim((string) ($_SERVER['HTTP_X_API_TOKEN'] ?? ''));
    }
}
