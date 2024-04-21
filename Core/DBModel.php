<?php

namespace app\Core;

use PDOStatement;

/**
 * Class DBModel
 *
 * This class is responsible for handling the database model operations.
 * It extends the base Model class and uses the PDO class to execute the queries.
 *
 * @package app\Core
 */
abstract class DBModel extends Model
{
    /**
     * @var string $id The id of the model.
     */
    public string $id;

    /**
     * Constructs a new DBModel object.
     *
     * This method takes an associative array of attributes as input and assigns each attribute to the corresponding property of the object.
     * The keys of the array are the property names and the values of the array are the property values.
     *
     * @param array $attributes An associative array of attributes to assign to the object.
     */
    protected function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Returns the name of the table associated with this model.
     *
     * @return string The name of the table.
     */
    abstract public static function tableName(): string;

    /**
     * Returns an array of the attributes of this model.
     *
     * @return array The attributes of the model.
     */
    abstract public function attributes(): array;

    /**
     * Returns an array of the default attributes of this model.
     *
     * @return array The default attributes of the model.
     */
    protected function defaultAttributes(): array
    {
        return ['id', 'deleted_at'];
    }

    /**
     * Returns the primary key of this model.
     *
     * @return string The primary key of the model.
     */
    public static function primaryKey(): string
    {
        return 'id';
    }

    /**
     * Saves the current model to the database.
     *
     * This method inserts a new record into the table associated with this model.
     * It prepares an INSERT SQL statement, binds the model's attributes to the statement, and executes it.
     * It always returns true after executing the statement.
     *
     * @return bool Always returns true.
     */
    public function save(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn ($attr) => ":$attr", $attributes);

        $statement = self::prepare("INSERT INTO $tableName (" . implode(", ", $attributes) . ")
            VALUES (" . implode(', ', $params) . ");
        ");

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    /**
     * Marks a record as deleted in the database.
     *
     * This method sets the 'deleted_at' field of the record with the current model's id to the current timestamp.
     * It prepares and executes an UPDATE SQL statement to do this.
     * It always returns true after executing the statement.
     *
     * @return bool Always returns true.
     */
    public function delete() : bool
    {
        $tableName = $this->tableName();
        $id = $this->id;

        $statement = self::prepare("UPDATE $tableName SET deleted_at = NOW() WHERE id = :id");
        $statement->bindValue(':id', $id);

        $statement->execute();
        return true;
    }

    /**
     * Updates the record in the database.
     *
     * This method prepares and executes an UPDATE SQL statement to update the record with the current model's id.
     * It sets the attributes of the model to the corresponding fields in the database.
     * It always returns true after executing the statement.
     *
     * @return bool Always returns true.
     */
    public function update() : bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $id = $this->id;

        $sql = "UPDATE $tableName SET ";
        foreach ($attributes as $attribute) {
            $sql .= "$attribute = :$attribute, ";
        }
        $sql = rtrim($sql, ', ') . " WHERE id = :id";

        $statement = self::prepare($sql);

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->bindValue(':id', $id);

        $statement->execute();
        return true;
    }

    /**
     * Prepares a SQL query and returns the PDO statement.
     *
     * This method takes an SQL query as input, prepares it, and returns the PDO statement.
     *
     * @param string $sql The SQL query to prepare.
     * @return bool|PDOStatement The PDO statement.
     */
    public static function prepare(string $sql): bool|PDOStatement
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    /**
     * Executes a SQL query and returns the result.
     *
     * This method takes an SQL query as input, executes it, and returns the result.
     *
     * @param array $where The SQL query to execute.
     * @return bool|PDOStatement The result of the query.
     */
    public static function findOne(array $where): bool|PDOStatement
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND", array_map(fn ($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
}