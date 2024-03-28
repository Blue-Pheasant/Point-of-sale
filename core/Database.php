<?php

namespace app\core;

use PDO;
use PDOException;
use app\models\Product;

class Database
{
    public PDO $pdo;
    private $dsn;
    private $user;
    private $password;
    private static $instance = NULl;

    public static function getInstance()
    {
        $dsn = $_ENV['DB_DSN'];
        $user =  $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        if (!isset(self::$instance)) {
            try {
                self::$instance = new PDO($dsn, $user, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->exec("SET client_encoding TO 'UTF8'");
            } catch (PDOException $ex) {
                die($ex->getMessage());
            }
        }
        return self::$instance;
    }

    public function __construct($config)
    {
        $this->dsn = $config['dsn'] ? $config['dsn'] : $_ENV['DB_DSN'];
        $this->user = $config['user'] ? $config['user'] : $_ENV['DB_USER'];
        $this->password = $config['password'] ? $config['password'] : $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET client_encoding TO 'UTF8'");
        } catch (PDOException $exp) {
            echo "Connection to database failed: " . $exp->getMessage();
        }
    }

    public function CreateConnection()
    {
        return $this->dbo;
    }

    public function applyMigrations()
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
            $this->log("Applyied migration $migration" . PHP_EOL);
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied.\n");
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(",", array_map(fn ($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

   
    public function query($message)
    {
        return $this->pdo->query($message);
    }
}