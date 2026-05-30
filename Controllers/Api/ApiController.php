<?php

namespace app\Controllers\Api;

use app\Core\Application;
use app\Core\Controller;

/**
 * Base controller for the JSON REST API (roadmap T23).
 *
 * Every API endpoint returns JSON through {@see \app\Core\Response::json()}
 * (T08) rather than rendering a view, so these helpers centralise the success
 * and error envelopes and the matching HTTP status codes. API errors never
 * leak an HTML error page — they always come back as JSON.
 *
 * @package app\Controllers\Api
 */
abstract class ApiController extends Controller
{
    /**
     * Sends a success envelope and stops execution.
     *
     * @param mixed $data The payload to return under `data`.
     * @param int $status The HTTP status code (default 200).
     * @return void
     */
    protected function respond(mixed $data, int $status = 200): void
    {
        Application::$app->response->json(['data' => $data], $status);
    }

    /**
     * Sends a paginated success envelope (data + pagination) and stops.
     *
     * @param array<int, mixed> $data The page of items.
     * @param array<string, mixed> $pagination The pagination metadata.
     * @param int $status The HTTP status code (default 200).
     * @return void
     */
    protected function respondPaginated(array $data, array $pagination, int $status = 200): void
    {
        Application::$app->response->json([
            'data'       => $data,
            'pagination' => $pagination,
        ], $status);
    }

    /**
     * Sends an error envelope and stops execution.
     *
     * @param string $message A human-readable error message.
     * @param int $status The HTTP status code (default 400).
     * @return void
     */
    protected function error(string $message, int $status = 400): void
    {
        Application::$app->response->json([
            'error' => ['message' => $message],
        ], $status);
    }
}
