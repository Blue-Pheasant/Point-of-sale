<?php

namespace app\core;

class Response
{
    public static function setStateCode(int $code)
    {
        http_response_code($code);
    }

    public static function redirect(string $url)
    {
        header('Location: ' . $url);
    }
}