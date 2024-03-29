<?php

namespace app\core;

use app\core\Application;
use app\core\Database;
use PDO;
use PDOException;

abstract class DBModel extends Model
{
    protected string $id;
    protected $deleted_at = null;

    protected function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    abstract public static function tableName(): string;

    abstract public function attributes(): array;

    protected function defaultAttributes(): array
    {
        return ['id', 'deleted_at'];
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function save()
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

    public function delete() : bool
    {
        $tableName = $this->tableName();
        $id = $this->id;

        $statement = self::prepare("UPDATE $tableName SET deleted_at = NOW() WHERE id = :id");
        $statement->bindValue(':id', $id);

        $statement->execute();
        return true;
    }

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

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function findOne($where)
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