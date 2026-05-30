<?php

namespace app\Core\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Thrown by {@see Container::get()} when no entry or class can be resolved for
 * the requested id. PSR-11 compliant ({@see NotFoundExceptionInterface}).
 */
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
}
