<?php

namespace app\Exception;

class NotFoundException extends \RuntimeException
{
    protected $message = 'Not found';
    protected $code    = 404;
}
