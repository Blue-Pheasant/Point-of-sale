<?php

namespace App\Core;

abstract class Route
{
    protected array $routes = [];

    public function __construct()
    {
        $this->register();
    }

    abstract public function register();

    public function routes() : array
    {
        return $this->routes;
    }
 
    public function get($path, callable $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, callable $callback)
    {
        $this->routes['post'][$path] = $callback;
    }
}