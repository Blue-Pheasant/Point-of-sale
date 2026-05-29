<?php

namespace app\Middlewares;

use app\Core\Middleware;
use app\Core\Session;
use app\Exception\ForbiddenException;

/**
 * Verifies CSRF token for state-changing HTTP methods (POST/PUT/PATCH/DELETE).
 *
 * The token is generated once per session via Csrf::token() and embedded in
 * forms with the csrf_field() helper. On mutation requests the middleware
 * checks $_POST['_csrf'] or the X-CSRF-Token header against the session token.
 */
class CsrfMiddleware extends Middleware
{
    private const MUTATION_METHODS = ['post', 'put', 'patch', 'delete'];
    private const TOKEN_KEY        = '_csrf_token';

    public function execute(): void
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');

        if (!in_array($method, self::MUTATION_METHODS, true)) {
            return;
        }

        $sessionToken = Session::get(self::TOKEN_KEY);
        $requestToken = $_POST['_csrf']
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if (!$sessionToken || !$requestToken || !hash_equals((string) $sessionToken, (string) $requestToken)) {
            throw new ForbiddenException();
        }
    }

    /**
     * Return (and lazily create) the session CSRF token.
     */
    public static function token(): string
    {
        if (!Session::exists(self::TOKEN_KEY)) {
            Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }
        return (string) Session::get(self::TOKEN_KEY);
    }
}
