<?php

namespace app\Middlewares;

use app\Auth\AuthUser;
use app\Core\Middleware;
use app\Exception\ForbiddenException;

class AdminMiddleware extends Middleware
{
    public function execute(): void
    {
        if (!AuthUser::isAdmin() && !empty($this->actions)) {
            if (in_array($this->currentAction(), $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}
