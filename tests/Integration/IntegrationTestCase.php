<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Core\Database;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Base class for integration tests (roadmap T18).
 *
 * Spins up a single in-memory SQLite database for the whole process, builds a
 * SQLite-compatible copy of the production schema, and wires that connection
 * into every place the application resolves a PDO from:
 *   - {@see Database::$instance} (used by {@see \app\Common\Query} and the
 *     services' `Database::getInstance()` calls);
 *   - a real {@see Database} stored on {@see \app\Core\Application::$app->db}
 *     (used by {@see \app\Core\DBModel} via `Application::$app->db->pdo`).
 *
 * Tests stay isolated by truncating every data table in {@see setUp()} — each
 * test starts against an empty schema and seeds only what it needs.
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static PDO $pdo;

    /** Tables wiped before each test (migrations table is left alone). */
    private const TABLES = [
        'order_detail',
        'cart_item',
        'orders',
        'cart',
        'products',
        'categories',
        'users',
        'stores',
    ];

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        self::$pdo->exec('PRAGMA foreign_keys = ON');

        self::migrate(self::$pdo);
        self::injectConnection(self::$pdo);
    }

    protected function setUp(): void
    {
        // Isolate tests: every table starts empty (children seed their own data).
        self::$pdo->exec('PRAGMA foreign_keys = OFF');
        foreach (self::TABLES as $table) {
            self::$pdo->exec("DELETE FROM $table");
        }
        self::$pdo->exec('PRAGMA foreign_keys = ON');
    }

    /**
     * Builds a SQLite-compatible schema mirroring the production tables that the
     * services touch. Types/defaults are simplified to their SQLite equivalents
     * (TEXT ids, `CURRENT_TIMESTAMP` defaults) while keeping column names and
     * the `deleted_at` soft-delete convention identical to production.
     */
    protected static function migrate(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE categories (
                id TEXT PRIMARY KEY,
                name TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE products (
                id TEXT PRIMARY KEY,
                category_id TEXT NOT NULL,
                name TEXT NOT NULL,
                image_url TEXT NOT NULL DEFAULT "",
                price INTEGER NOT NULL DEFAULT 0,
                description TEXT NOT NULL DEFAULT "",
                stock_quantity INTEGER NOT NULL DEFAULT 0,
                low_stock_threshold INTEGER NOT NULL DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE users (
                id TEXT PRIMARY KEY,
                firstname TEXT NOT NULL DEFAULT "",
                lastname TEXT NOT NULL DEFAULT "",
                email TEXT NOT NULL,
                phone_number TEXT NOT NULL DEFAULT "",
                password TEXT NOT NULL DEFAULT "",
                image_url TEXT DEFAULT NULL,
                address TEXT DEFAULT NULL,
                role TEXT NOT NULL DEFAULT "user",
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE cart (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT "active",
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE cart_item (
                id TEXT PRIMARY KEY,
                product_id TEXT NOT NULL,
                cart_id TEXT NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                size TEXT NOT NULL DEFAULT "",
                note TEXT NOT NULL DEFAULT "",
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE orders (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                payment_method TEXT NOT NULL DEFAULT "",
                delivery_name TEXT NOT NULL DEFAULT "",
                delivery_phone TEXT NOT NULL DEFAULT "",
                delivery_address TEXT NOT NULL DEFAULT "",
                status TEXT NOT NULL DEFAULT "processing",
                display TEXT DEFAULT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE order_detail (
                id TEXT PRIMARY KEY,
                product_id TEXT NOT NULL,
                order_id TEXT NOT NULL,
                size TEXT NOT NULL DEFAULT "",
                note TEXT NOT NULL DEFAULT "",
                quantity INTEGER NOT NULL DEFAULT 1,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE stores (
                id TEXT PRIMARY KEY,
                address TEXT NOT NULL DEFAULT "",
                status TEXT NOT NULL DEFAULT "",
                image_url TEXT NOT NULL DEFAULT "",
                open_time TEXT NOT NULL DEFAULT "",
                phone TEXT NOT NULL DEFAULT "",
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                deleted_at TEXT DEFAULT NULL
            )'
        );
    }

    /**
     * Points both the static {@see Database::$instance} and a fresh
     * {@see Database} on {@see \app\Core\Application::$app} at the given PDO, so
     * the whole data layer talks to the in-memory database. Also resets the
     * cached connection inside {@see \app\Common\Query} if one was captured.
     */
    private static function injectConnection(PDO $pdo): void
    {
        // Database::$instance (private static) → the shared getInstance() handle.
        $instance = new ReflectionProperty(Database::class, 'instance');
        $instance->setAccessible(true);
        $instance->setValue(null, $pdo);

        // A real Database whose ->pdo is the SQLite handle, for DBModel::prepare().
        $database = (new ReflectionClass(Database::class))->newInstanceWithoutConstructor();
        $database->pdo = $pdo;

        TestApplication::boot($database);

        // Query::$db caches its own handle lazily; clear it so it re-resolves.
        $queryDb = new ReflectionProperty(\app\Common\Query::class, 'db');
        $queryDb->setAccessible(true);
        $queryDb->setValue(null, $pdo);
    }

    // -- seeding helpers shared by the service tests ------------------------

    protected function seedCategory(string $id = 'cat-1', string $name = 'Coffee'): void
    {
        self::$pdo->prepare('INSERT INTO categories (id, name) VALUES (:id, :name)')
            ->execute(['id' => $id, 'name' => $name]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    protected function seedProduct(array $overrides = []): string
    {
        $row = array_merge([
            'id'             => 'prod-' . count($this->existingIds('products')),
            'category_id'    => 'cat-1',
            'name'           => 'Espresso',
            'image_url'      => '',
            'price'          => 25000,
            'description'    => 'A strong short black coffee.',
            'stock_quantity' => 10,
        ], $overrides);

        self::$pdo->prepare(
            'INSERT INTO products (id, category_id, name, image_url, price, description, stock_quantity)
             VALUES (:id, :category_id, :name, :image_url, :price, :description, :stock_quantity)'
        )->execute($row);

        return $row['id'];
    }

    protected function seedUser(string $id = 'user-1', string $email = 'user@example.com'): void
    {
        self::$pdo->prepare(
            'INSERT INTO users (id, email, firstname, lastname) VALUES (:id, :email, "Test", "User")'
        )->execute(['id' => $id, 'email' => $email]);
    }

    /** @return array<int, string> */
    protected function existingIds(string $table): array
    {
        return self::$pdo->query("SELECT id FROM $table")->fetchAll(PDO::FETCH_COLUMN);
    }
}
