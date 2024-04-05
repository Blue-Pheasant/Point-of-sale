<?php

namespace app\middlewares;

use app\core\Application;
use app\core\Middleware;
use app\exception\ForbiddenException;

class AdminMiddleware extends Middleware
{
    public function execute()
    {
        if (!Application::isAdmin() && !empty($this->actions)) {
            if(in_array($this->currentAction(), $this->actions)) 
            {
                throw new ForbiddenException();
            }
        }
    }
}
