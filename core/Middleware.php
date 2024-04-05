<?php

namespace app\core;

abstract class Middleware
{
    protected array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    protected function currentAction()
    {
        return Application::$app->controller->action;
    }

    abstract public function execute();
}