<?php

namespace app\Core;

/**
 * Class Response
 *
 * This class is responsible for handling the response operations of the application.
 *
 * @package app\Core
 */
class Response
{
    /**
     * Method setStatusCode
     *
     * Sets the status code of the response.
     *
     * @param int $code The status code of the response.
     */
    public static function setStateCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Method redirect
     *
     * Redirects to the given URL.
     *
     * @param string $url The URL to be redirected.
     */
    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
    }

    /**
     * Method back
     *
     * Redirects back to the previous page.
     */
    public function back(): void
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}