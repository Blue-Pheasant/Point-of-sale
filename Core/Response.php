<?php

namespace App\Core;

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

    public function back()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}