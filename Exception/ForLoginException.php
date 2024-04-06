<?php

namespace App\Exception;
use App\Core\Application;

class ForLoginException extends \Exception
{
    public function __construct()
    {
        $intended = Application::$app->request->getReqest();
        Application::$app->router->setIntendedUrl($intended);
        Application::$app->response->redirect('/login');
    }
}