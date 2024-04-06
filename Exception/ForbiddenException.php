<?php

namespace App\Exception;
use App\Core\Application;

class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;

    public function __construct()
    {
        Application::$app->controller->layout = 'auth';
        Application::$app->response->redirect('/error');
    }
}
