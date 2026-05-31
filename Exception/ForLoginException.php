<?php

namespace app\Exception;

class ForLoginException extends \RuntimeException
{
    public function __construct(string $intended = '')
    {
        $this->message = 'Authentication required';
        parent::__construct($this->message, 401);

        if ($intended !== '') {
            \app\Core\Application::$app->session->set('url.intended', $intended);
        }
    }
}
