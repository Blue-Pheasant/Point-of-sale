<?php

namespace App\Middlewares;

use App\Core\Application;
use App\Core\Middleware;
use App\Exception\ForbiddenException;

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
