<?php

namespace app\Exception;

class NotFoundException extends \Exception
{
    protected $message = 'Not found';
    protected $code = 403;

    public function __construct()
    {
        return $this->message;
    }
}