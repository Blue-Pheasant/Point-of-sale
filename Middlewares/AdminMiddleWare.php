<?php

namespace app\Middlewares;

use app\Core\Application;
use app\Core\Middleware;
use app\Exception\ForbiddenException;

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
