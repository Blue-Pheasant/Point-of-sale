<?php

namespace app\Core;

/**
 * Class Middleware
 *
 * This class is responsible for handling the middleware operations of the application.
 * It uses the Application class.
 *
 * @package app\Core
 */
abstract class Middleware
{
    /**
     * @var array $actions The actions of the middleware.
     */
    protected array $actions = [];

    /**
     * Middleware constructor.
     *
     * Initializes the actions of the middleware.
     *
     * @param array $actions The actions of the middleware.
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * Method currentAction
     *
     * Gets the current action.
     *
     * @return string
     */
    protected function currentAction(): string
    {
        return Application::$app->controller->action;
    }

    /**
     * Method execute
     *
     * Executes the middleware.
     */
    abstract public function execute();
}