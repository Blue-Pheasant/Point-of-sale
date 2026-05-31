<?php

namespace app\Core;

/**
 * Class Response
 *
 * Handles the outgoing response: status codes, redirects, and JSON output.
 * All methods are instance methods for a consistent API; reach the shared
 * instance via {@see Application::$app->response}.
 *
 * @package app\Core
 */
class Response
{
    /**
     * Sets the HTTP status code of the response.
     *
     * @param int $code The status code.
     * @return void
     */
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Sets the HTTP status code of the response.
     *
     * @deprecated Use {@see self::setStatusCode()}. Kept as a backward-compatible
     *     alias for the misspelled original name.
     *
     * @param int $code The status code.
     * @return void
     */
    public function setStateCode(int $code): void
    {
        $this->setStatusCode($code);
    }

    /**
     * Sends a JSON response with the given status code and stops execution.
     *
     * @param mixed $data The data to encode as JSON.
     * @param int $status The HTTP status code (default 200).
     * @return void
     */
    public function json(mixed $data, int $status = 200): void
    {
        $this->setStatusCode($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirects to the given URL and stops execution.
     *
     * The explicit `exit` prevents any code after the redirect from running,
     * which previously continued to execute and could emit unintended output.
     *
     * @param string $url The URL to redirect to.
     * @return void
     */
    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirects back to the referring page, falling back to a default when no
     * `HTTP_REFERER` header is present.
     *
     * @param string $fallback The path to use when no referer is available.
     * @return void
     */
    public function back(string $fallback = '/'): void
    {
        $this->redirect($_SERVER['HTTP_REFERER'] ?? $fallback);
    }
}
