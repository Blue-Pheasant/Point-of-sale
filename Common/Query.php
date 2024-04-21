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
            $stmt->bindValue(":$key", $value);
        }

        return $stmt;
    }

    /**
     * Executes a SQL query and returns the number of affected rows.
     *
     * @param string $table The table to execute the query on.
     * @param array $columns The columns to select.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @param int $limit The maximum number of rows to return.
     * @param int $offset The number of rows to skip.
     * @return int The number of affected rows.
     */
    public static function get(string $table, array $columns = [], array $where = [], int $limit = 0, int $offset = 0): string
    {
        $query = "SELECT ";
        if (count($columns) > 0) {
            $query .= implode(", ", $columns);
        } else {
            $query .= "*";
        }
        $query .= " FROM $table";

        if (count($where) > 0) {
            $query .= " WHERE ";
            $whereClause = [];
            foreach ($where as $key => $value) {
                $whereClause[] = "$key = '$value'";
            }
            $query .= implode(" AND ", $whereClause);
        }

        if ($limit > 0) {
            $query .= " LIMIT $limit";
        }

        if ($offset > 0) {
            $query .= " OFFSET $offset";
        }

        return $query;
    }

    /**
     * Inserts data into a table.
     * @param string $table The table to insert data into.
     * @param array $data The data to insert.
     */
    public static function insert(string $table, array $data): string
    {
        $query = "INSERT INTO $table (";
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $values[] = "'$value'";
        }
        $query .= implode(", ", $columns);
        $query .= ") VALUES (";
        $query .= implode(", ", $values);
        $query .= ")";
        return $query;
    }

    /**
     * Updates data in a table.
     * @param string $table The table to update data in.
     * @param array $data The data to update.
     * @param array $where The conditions to update data by.
     */
    public static function update(string $table, array $data, array $where): string
    {
        $query = "UPDATE $table SET ";
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = '$value'";
        }
        $query .= implode(", ", $set);
        $query .= " WHERE ";
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = '$value'";
        }
        $query .= implode(" AND ", $whereClause);
        return $query;
    }

    /**
     * Deletes data from a table.
     * @param string $table The table to delete data from.
     * @param array $where The conditions to delete data by.
     */
    public static function delete(string $table, array $where): string
    {
        $query = "DELETE FROM $table WHERE ";
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = '$value'";
        }
        $query .= implode(" AND ", $whereClause);
        return $query;
    }

    /**
     * Counts the number of rows in a table.
     * @param string $table The table to count rows in.
     * @param array $where The conditions to count rows by.
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