<?php

namespace App\Middlewares;

use App\Core\Application;
use App\Core\Middleware;
use App\Exception\ForLoginException;

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
