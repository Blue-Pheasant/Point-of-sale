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
     * @var PDO $pdo The PDO instance.
     */
    public PDO $pdo;

    /**
     * @var string $dsn The DSN of the database.
     */
    private string $dsn;

    /**
     * @var string $user The user of the database.
     */
    private string $user;

    /**
     * @var string $password The password of the database.
     */
    private string $password;

    /**
     * @var PDO|null $instance The database instance.
     */
    private static PDO|null $instance = NULl;

    /**
     * Method getInstance
     *
     * Gets the database instance.
     *
     * @return Database|PDO|null
     */
    public static function getInstance(): Database|PDO|null
    {
        $dsn = $_ENV['DB_DSN'];
        $user =  $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        if (!isset(self::$instance)) {
            try {
                self::$instance = new PDO($dsn, $user, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->exec("SET NAMES 'utf8'");
            } catch (PDOException $ex) {
                die($ex->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * Database constructor.
     *
     * Initializes the database with the provided configuration.
     *
     * @param array $config The configuration of the database.
     */
    public function __construct(array $config)
    {
        $this->dsn = $config['dsn'] ?: $_ENV['DB_DSN'];
        $this->user = $config['user'] ?: $_ENV['DB_USER'];
        $this->password = $config['password'] ?: $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET NAMES 'utf8'");
        } catch (PDOException $exp) {
            echo "Connection to database failed: " . $exp->getMessage();
        }
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