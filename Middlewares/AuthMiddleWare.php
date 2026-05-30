<?php

namespace app\Middlewares;

use app\Auth\AuthUser;
use app\Core\Middleware;
use app\Exception\ForLoginException;

class AuthMiddleware extends Middleware
{
    public function execute(): void
    {
        if (AuthUser::isGuest() && !empty($this->actions)) {
            if (in_array($this->currentAction(), $this->actions)) {
                $intended = \app\Core\Application::$app->request->getRequest();
                throw new ForLoginException($intended);
            }
        }
    }
}
