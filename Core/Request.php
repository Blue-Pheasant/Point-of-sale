<?php

namespace app\Core;

/**
 * Class Request
 *
 * This class is responsible for handling the request operations of the application.
 *
 * @package app\Core
 */
class Request
{
    /**
     * Method getPath
     *
     * Gets the path of the request.
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
     * Retrieves a specific parameter from the GET request.
     *
     * This method checks if the specified parameter exists in the $_GET super-global array.
     * If the parameter exists, it returns its value. If it does not exist, the method returns null.
     *
     * @param string $param The name of the parameter to retrieve.
     * @return mixed The value of the parameter if it exists, null otherwise.
     */
    public function getParam(string $param): mixed
    {
        if (isset($_GET[$param])) {
            return $_GET[$param];
        }

        return null;
    }

    /**
     * Retrieves the request method.
     *
     * This method returns the request method from the $_SERVER super-global array.
     *
     * @return string The request method.
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Retrieves the request URI.
     *
     * This method returns the request URI from the $_SERVER super-global array.
     *
     * @return string The request URI.
     */
    public function getRequest(): string
    {
        return "$_SERVER[REQUEST_URI]";
    }

    /**
     * Checks if the request method is GET.
     *
     * This method checks if the request method is GET and returns true if it is, false otherwise.
     *
     * @return bool True if the request method is GET, false otherwise.
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    /**
     * Checks if the request method is POST.
     *
     * This method checks if the request method is POST and returns true if it is, false otherwise.
     *
     * @return bool True if the request method is POST, false otherwise.
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    /**
     * Retrieves the request body.
     *
     * This method returns the request body from the $_POST super-global array.
     *
     * @return array The request body.
     */
    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    /**
     * Retrieves all parameters from the current HTTP request.
     *
     * This method retrieves all parameters from the $_GET or $_POST super-global arrays, depending on the HTTP method of the request.
     * It sanitizes the parameters using the FILTER_SANITIZE_SPECIAL_CHARS filter.
     * It returns an associative array of the parameters, where the keys are the parameter names and the values are the sanitized parameter values.
     *
     * @return array An associative array of the sanitized parameters.
     */
    public function getPrams(): array
    {
        $params = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $params[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $params[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $params;
    }
}