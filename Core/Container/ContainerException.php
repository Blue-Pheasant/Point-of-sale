<?php

namespace app\Core\Container;

use Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * Thrown when an entry cannot be built — e.g. a non-instantiable class, a
 * circular dependency, or an unresolvable required constructor parameter.
 * PSR-11 compliant ({@see ContainerExceptionInterface}).
 */
class ContainerException extends Exception implements ContainerExceptionInterface
{
}
