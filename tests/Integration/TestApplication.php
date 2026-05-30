<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Core\Application;
use app\Core\Database;
use ReflectionClass;

/**
 * Builds the minimal {@see Application::$app} singleton the data layer needs in
 * tests, without running the real constructor (which would open a MySQL
 * connection and start a session).
 *
 * Only {@see Application::$db} is populated — that is all {@see \app\Core\DBModel}
 * relies on (`Application::$app->db->pdo`). The rest of the application graph
 * (router, session, view) is left unset because the integration tests exercise
 * services and models directly, never the HTTP layer.
 */
final class TestApplication
{
    public static function boot(Database $database): void
    {
        $app = (new ReflectionClass(Application::class))->newInstanceWithoutConstructor();
        $app->db = $database;

        Application::$app = $app;
    }
}
