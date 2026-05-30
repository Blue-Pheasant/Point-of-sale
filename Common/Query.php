<?php

namespace app\Common;

use app\Core\Database;
use PDO;
use PDOStatement;

/**
 * Class Query
 *
 * This class provides a static method to prepare SQL queries.
 *
 * @package app\Common
 */
class Query
{
    /**
     * @var PDO The PDO instance used to interact with the database.
     */
    private static PDO $db;

    /**
     * Prepares a SQL query and binds the provided parameters to it.
     *
     * @param string $query The SQL query to prepare.
     * @param array $params The parameters to bind to the query.
     * @return PDOStatement The prepared statement.
     */
    public static function prepare(string $query, array $params = []): PDOStatement
    {
        if (empty(self::$db)) {
            self::$db = Database::getInstance();
        }

        $stmt = self::$db->prepare($query);

        foreach ($params as $key => $value) {
            // Bind the correct PDO type. With PDO::ATTR_EMULATE_PREPARES = false,
            // integers (e.g. LIMIT/OFFSET) must be bound as PARAM_INT, otherwise
            // MySQL receives quoted strings and raises a syntax error.
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };

            $stmt->bindValue(":$key", $value, $type);
        }

        return $stmt;
    }

    /**
     * Starts a new fluent {@see QueryBuilder} for the given table.
     *
     * Preferred entry point for new code: it always binds values as
     * parameters, so SQL injection is impossible by construction.
     *
     * @param string $table The table to operate on.
     * @return QueryBuilder
     */
    public static function table(string $table): QueryBuilder
    {
        return QueryBuilder::table($table);
    }

    /**
     * Fetches rows from a table.
     *
     * @deprecated Use {@see Query::table()} / {@see QueryBuilder} instead. This
     *     wrapper now binds every value as a parameter (no string concatenation)
     *     and EXECUTES the query, returning the fetched rows. Note: unlike the
     *     previous version it returns rows, not a raw SQL string.
     *
     * @param string $table The table to select from.
     * @param array<int, string> $columns The columns to select.
     * @param array<string, mixed> $where Equality conditions (column => value).
     * @param int $limit The maximum number of rows to return (0 for none).
     * @param int $offset The number of rows to skip (0 for none).
     * @return array<int, array<string, mixed>> The fetched rows.
     */
    public static function get(string $table, array $columns = [], array $where = [], int $limit = 0, int $offset = 0): array
    {
        $builder = QueryBuilder::table($table)->select($columns);

        foreach ($where as $key => $value) {
            $builder->where($key, $value);
        }

        if ($limit > 0) {
            $builder->limit($limit);
        }

        if ($offset > 0) {
            $builder->offset($offset);
        }

        return $builder->get();
    }

    /**
     * Inserts data into a table.
     *
     * @deprecated Use {@see Query::table()} / {@see QueryBuilder::insert()}.
     *     This wrapper binds every value as a parameter and executes the insert.
     *
     * @param string $table The table to insert data into.
     * @param array<string, mixed> $data The data to insert.
     * @return bool True on success, false otherwise.
     */
    public static function insert(string $table, array $data): bool
    {
        return QueryBuilder::table($table)->insert($data);
    }

    /**
     * Updates data in a table.
     *
     * @deprecated Use {@see Query::table()} / {@see QueryBuilder::update()}.
     *     This wrapper binds every value as a parameter and executes the update.
     *
     * @param string $table The table to update.
     * @param array<string, mixed> $data The data to set.
     * @param array<string, mixed> $where Equality conditions (column => value).
     * @return bool True on success, false otherwise.
     */
    public static function update(string $table, array $data, array $where): bool
    {
        $builder = QueryBuilder::table($table);
        foreach ($where as $key => $value) {
            $builder->where($key, $value);
        }

        return $builder->update($data);
    }

    /**
     * Deletes data from a table.
     *
     * @deprecated Use {@see Query::table()} / {@see QueryBuilder::delete()}.
     *     This wrapper binds every value as a parameter and executes the delete.
     *
     * @param string $table The table to delete from.
     * @param array<string, mixed> $where Equality conditions (column => value).
     * @return bool True on success, false otherwise.
     */
    public static function delete(string $table, array $where): bool
    {
        $builder = QueryBuilder::table($table);
        foreach ($where as $key => $value) {
            $builder->where($key, $value);
        }

        return $builder->delete();
    }

    /**
     * Counts the number of rows returned by a (param-bound) query.
     *
     * @param string $query The SQL query to wrap in a COUNT(*).
     * @param array<string, mixed> $params The parameters to bind to the query.
     * @return int The row count.
     */
    public static function getCount(string $query, array $params = []): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM ($query) AS count");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Fetches all rows from a table.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     */
    public static function getAll(string $query, array $params = []): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
