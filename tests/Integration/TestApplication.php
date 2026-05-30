<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Core\Application;
use app\Core\Container\Container;
use app\Core\Database;
use PDO;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Builds the minimal {@see Application::$app} singleton the data layer needs in
 * tests, without running the real constructor (which would open a MySQL
 * connection and start a session).
 *
 * {@see Application::$db} is populated for {@see \app\Core\DBModel} (which reads
 * `Application::$app->db->pdo`), and a {@see Container} is wired with the same
 * {@see Database}/{@see PDO} bound — so services can be resolved through the
 * container, or constructed with an injected PDO, exactly as at runtime.
 */
final class TestApplication
{
    public static function boot(Database $database): void
    {
        $app = (new ReflectionClass(Application::class))->newInstanceWithoutConstructor();
        $app->db = $database;

        $container = new Container();
        $container->instance(Container::class, $container);
        $container->instance(ContainerInterface::class, $container);
        $container->instance(Application::class, $app);
        $container->instance(Database::class, $database);
        $container->instance(PDO::class, $database->pdo);
        $app->container = $container;

        Application::$app = $app;
    }
}
