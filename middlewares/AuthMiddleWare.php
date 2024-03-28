<?php

namespace app\middlewares;

use app\core\Application;
use app\exception\ForLoginException;

class AuthMiddleware extends BaseMiddleware
{
    protected array $actions;

    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (Application::isGuest()) {
            if (!empty($this->actions)) {
                    if(in_array(Application::$app->controller->action, $this->actions)) {
                        throw new ForLoginException();
                }
            }
        }
    }
}
