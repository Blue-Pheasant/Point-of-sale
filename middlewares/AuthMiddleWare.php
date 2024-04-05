<?php

namespace app\middlewares;

use app\core\Application;
use app\core\Middleware;
use app\exception\ForLoginException;

class AuthMiddleware extends Middleware
{
    public function execute()
    {
        if (Application::isGuest() && !empty($this->actions)) {
            if(in_array($this->currentAction(), $this->actions)) 
            {
                throw new ForLoginException();
            }
        }
    }
}
