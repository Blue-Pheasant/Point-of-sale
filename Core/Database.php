<?php

namespace app\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Database
 *
 * This class is responsible for handling the database operations.
 * It uses the PDO class to connect to the database and execute the queries.
 *
 * @package app\Core
 */
class Database
{
    /**
     * @var PDO $pdo The PDO instance (kept public as a backward-compatible alias).
     */
    public PDO $pdo;

    /**
     * @var PDO|null $instance The shared PDO instance behind getInstance().
     */
    private static ?PDO $instance = null;

    /**
     * Database constructor.
     *
     * Initializes the database with the provided configuration. Both this
     * instance's {@see self::$pdo} and the static {@see self::$instance} share
     * the SAME PDO connection, so {@see self::getInstance()} and `->pdo` always
     * return the one canonical connection.
     *
     * @param array $config The configuration of the database.
     */
    public function __construct(array $config)
    {
        $this->pdo = self::connect(
            $config['dsn'] ?: $_ENV['DB_DSN'],
            $config['user'] ?: $_ENV['DB_USER'],
            $config['password'] ?: $_ENV['DB_PASSWORD']
        );

        self::$instance = $this->pdo;
    }

    /**
     * Creates a configured PDO connection.
     *
     * Single source of truth for connection options:
     * exceptions on error, real (non-emulated) prepared statements,
     * associative fetch by default, and the `utf8mb4` charset that matches
     * the database collation (`utf8mb4_vietnamese_ci`).
     *
     * @param string $dsn The PDO DSN.
     * @param string $user The database user.
     * @param string $password The database password.
     * @return PDO The configured PDO connection.
     * @throws PDOException When the connection cannot be established. The
     *         exception is left for the central error handler (roadmap T12).
     */
    private static function connect(string $dsn, string $user, string $password): PDO
    {
        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
        ]);
    }

    /**
     * Method getInstance
     *
     * Returns the shared PDO connection. Kept as a backward-compatible alias
     * for callers (e.g. {@see \app\Common\Query}, {@see \app\Services\AuthService})
     * that obtain the connection statically. Lazily connects from environment
     * configuration if the application has not constructed a Database yet.
     *
     * @return PDO The shared PDO connection.
     * @throws PDOException When the connection cannot be established.
     */
    public static function getInstance(): PDO
    {
        if (!isset(self::$instance)) {
            self::$instance = self::connect(
                $_ENV['DB_DSN'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
        }

        return self::$instance;
    }

    /**
     * Method applyMigrations
     *
     * Applies the migrations to the database.
     */
    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $appliedMigrations =  $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR . '/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className;
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration" . PHP_EOL);
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied.\n");
        }
    }

    /**
     * Creates the migrations table.
     *
     * This method creates a new table named 'migrations' in the database if it doesn't already exist.
     * The 'migrations' table has the following columns:
     * - 'id': an auto-incrementing integer that serves as the primary key.
     * - 'migration': a string that stores the name of the migration.
     * - 'created_at': a timestamp that stores the date and time when the migration was created. It defaults to the current timestamp.
     *
     * @return void
     */
    public function createMigrationsTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
    }

    /**
     * Gets the applied migrations.
     *
     * This method retrieves the applied migrations from the 'migrations' table.
     *
     * @return bool|array The applied migrations.
     */
    public function getAppliedMigrations(): bool|array
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Saves the migrations to the database.
     *
     * This method takes an array of migration names, prepares an INSERT statement, and executes it to save the migrations to the 'migrations' table in the database.
     *
     * @param array $migrations An array of migration names to save to the database.
     * @return void
     */
    public function saveMigrations(array $migrations): void
    {
        $str = implode(",", array_map(fn ($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }

    /**
     * Logs a message with a timestamp.
     *
     * This method takes a message as input, prepends a timestamp to it, and echoes it followed by a newline.
     * The timestamp is in the 'Y-m-d H:i:s' format.
     *
     * @param string $message The message to log.
     * @return void
     */
    protected function log(string $message): void
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }

    /**
     * Prepares a SQL query and returns the PDO statement.
     *
     * This method takes an SQL query as input, prepares it, and returns the PDO statement.
     *
     * @param string $sql The SQL query to prepare.
     * @return bool|PDOStatement The PDO statement.
     */
    public function prepare(string $sql): bool|PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    /**
     * Executes a SQL query and returns the result.
     *
     * This method takes an SQL query as input, executes it, and returns the result.
     *
     * @param string $sql The SQL query to execute.
     * @return bool|PDOStatement The result of the query.
     */
    public function query(string $sql): bool|PDOStatement
    {
        return $this->pdo->query($sql);
    }
}