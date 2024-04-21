<?php

namespace app\Core;

/**
 * Class Route
 *
 * This class is responsible for handling the routes of the application.
 *
 * @package app\Core
 */
abstract class Route
{
    /**
     * @var array $routes The routes of the application.
     */
    protected array $routes = [];

    /**
     * Route constructor.
     */
    public function __construct()
    {
        $this->register();
    }

    /**
     * Method register
     *
     * Registers the routes of the application.
     */
    abstract public function register();

    /**
     * Method routes
     *
     * Gets the routes of the application.
     *
     * @return array
     */
    public function routes() : array
    {
        return $this->routes;
    }
 
    /**
     * Method get
     *
     * Adds a GET route to the application.
     *
     * @param string $path The path of the route.
     * @param string $callback The callback function of the route.
     */
    public function get(string $path, string $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Method post
     *
     * Adds a POST route to the application.
     *
     * @param string $path The path of the route.
     * @param string $callback The callback function of the route.
     */
    public function post(string $path, string $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }
}