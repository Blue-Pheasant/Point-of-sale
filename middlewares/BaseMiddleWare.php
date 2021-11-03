<?php

namespace app\middlewares;

abstract class BaseMiddleware
{
    abstract public function execute();
}