<?php

namespace App\Exception;

class NotFoundException extends \Exception
{
    protected $message = 'Not found';
    protected $code = 403;

    public function __construct()
    {
        return $this->message;
    }
}