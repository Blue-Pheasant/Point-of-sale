<?php

namespace app\Exception;

class ForbiddenException extends \RuntimeException
{
    protected $message = "You don't have permission to access this page";
    protected $code    = 403;
}
