<?php

namespace app\Common;

use app\core\Database;

class Query
{
    public static function get($table, $columns = [], $where = [], $limit = 0, $offset = 0)
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

    public static function insert($table, $data)
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

    public static function update($table, $data, $where)
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

    public static function delete($table, $where)
    {
        $query = "DELETE FROM $table WHERE ";
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = '$value'";
        }
        $query .= implode(" AND ", $whereClause);
        return $query;
    }

    public static function getCount($query) : int
    {
        $db = Database::getInstance();
        return $db->query("SELECT COUNT(*) FROM ($query) as count")->fetch()[0];
    }
}