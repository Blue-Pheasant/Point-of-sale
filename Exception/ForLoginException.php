<?php

namespace app\Exception;
use app\Core\Application;

class ForLoginException extends \Exception
{
    public function __construct()
    {
        $intended = Application::$app->request->getRequest();
        Application::$app->router->setIntendedUrl($intended);
        Application::$app->response->redirect('/login');
    }
}