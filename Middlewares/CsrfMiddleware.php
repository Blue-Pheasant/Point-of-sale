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

    /**
     * Path prefixes exempt from the session-cookie CSRF check. These endpoints
     * are stateless and authenticated by another mechanism that CSRF tokens do
     * not fit: `/api/*` uses a bearer token (no session cookie to forge), and
     * the payment callback is a server-to-server request authenticated by the
     * gateway's HMAC signature.
     *
     * @var array<int, string>
     */
    private const EXEMPT_PREFIXES = ['/api/', '/payment/callback'];

    public function execute(): void
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');

        if (!in_array($method, self::MUTATION_METHODS, true)) {
            return;
        }

        if ($this->isExempt()) {
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
     * Whether the current request path is exempt from the CSRF check.
     */
    private function isExempt(): bool
    {
        $path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        foreach (self::EXEMPT_PREFIXES as $prefix) {
            // Match the prefix exactly or only at a path boundary, so a prefix
            // like `/payment/callback` never accidentally exempts an unrelated
            // path such as `/payment/callbackX`.
            if ($path === $prefix
                || $path === rtrim($prefix, '/')
                || str_starts_with($path, rtrim($prefix, '/') . '/')) {
                return true;
            }
        }

        return false;
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
