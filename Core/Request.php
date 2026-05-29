<?php

namespace app\Core;

/**
 * Class Request
 *
 * Represents the incoming HTTP request: path, method (with `_method` spoofing
 * for HTML forms), query/body parameters, JSON bodies, and route parameters.
 *
 * Input is NOT sanitized here — values are returned as received. Escaping is
 * applied at output time (see the `e()` view helper, roadmap T11).
 *
 * @package app\Core
 */
class Request
{
    /**
     * @var array<string, string> The route parameters captured by the router.
     */
    private array $routeParams = [];

    /**
     * Gets the path of the request (without the query string).
     *
     * @return string
     */
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    /**
     * Retrieves a single query-string parameter.
     *
     * @param string $param The parameter name.
     * @return mixed The value if present, null otherwise.
     */
    public function getParam(string $param): mixed
    {
        return $_GET[$param] ?? null;
    }

    /**
     * Sets the route parameters captured by the router.
     *
     * @param array<string, string> $params The captured route parameters.
     * @return void
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Retrieves a single route parameter (e.g. `{id}` from `/products/{id}`).
     *
     * @param string $name The route parameter name.
     * @param mixed $default The default to return when absent.
     * @return mixed
     */
    public function routeParam(string $name, mixed $default = null): mixed
    {
        return $this->routeParams[$name] ?? $default;
    }

    /**
     * Retrieves all route parameters.
     *
     * @return array<string, string>
     */
    public function routeParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Retrieves the request method, honoring a `_method` override submitted by
     * HTML forms (method spoofing) so they can reach PUT/PATCH/DELETE routes.
     *
     * @return string The lowercase HTTP method.
     */
    public function getMethod(): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');

        if ($method === 'post' && isset($_POST['_method'])) {
            $spoofed = strtolower((string) $_POST['_method']);
            if (in_array($spoofed, ['put', 'patch', 'delete'], true)) {
                return $spoofed;
            }
        }

        return $method;
    }

    /**
     * Retrieves the raw request URI.
     *
     * @return string
     */
    public function getRequest(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Checks if the request method is GET.
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    /**
     * Checks if the request method is POST.
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    /**
     * Determines whether the client expects a JSON response, based on the
     * `Content-Type` or `Accept` headers.
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_contains($contentType, 'application/json')
            || str_contains($accept, 'application/json');
    }

    /**
     * Returns the request body parameters.
     *
     * For `application/json` requests the decoded JSON body is returned;
     * otherwise the `$_GET` or `$_POST` array is returned as-is (no lossy
     * sanitization — escaping happens at output time).
     *
     * @return array<string, mixed> The request body.
     */
    public function getBody(): array
    {
        if ($this->isJson()) {
            return $this->jsonBody();
        }

        if ($this->getMethod() === 'get') {
            return $_GET;
        }

        return $_POST;
    }

    /**
     * Retrieves all request parameters (query + body) without sanitization.
     *
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->getBody();
    }

    /**
     * @deprecated Misspelled original name. Use {@see self::getParams()}.
     *
     * @return array<string, mixed>
     */
    public function getPrams(): array
    {
        return $this->getParams();
    }

    /**
     * Checks whether the request carries a JSON body.
     *
     * @return bool
     */
    private function isJson(): bool
    {
        return str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    }

    /**
     * Decodes the raw JSON request body into an associative array.
     *
     * @return array<string, mixed> The decoded body, or an empty array.
     */
    private function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
