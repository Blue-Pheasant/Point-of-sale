<?php

namespace app\middlewares;


use app\core\Application;
use app\exception\ForbiddenException;

class AdminMiddleware extends BaseMiddleware
{
    public array $actions;

    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (!Application::isAdmin()) {
            if (!empty($this->actions)) {
                    if(in_array(Application::$app->controller->action, $this->actions)) {
                        throw new ForbiddenException();
                }
            }
        }
    }
}
