<?php

namespace app\Middlewares;

use app\Core\Middleware;
use app\Exception\ForbiddenException;
use app\Auth\AuthUser;

class AdminMiddleware extends Middleware
{
    public function execute()
    {
        if (!AuthUser::isAdmin() && !empty($this->actions)) {
            if(in_array($this->currentAction(), $this->actions)) 
            {
                throw new ForbiddenException();
            }
        }
    }
}
