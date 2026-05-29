<?php

namespace app\Core;

use app\Common\QueryBuilder;
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
     * Inserts a new record into the table associated with this model using the
     * param-bound {@see QueryBuilder}, and returns the real result of the
     * underlying statement execution.
     *
     * @return bool True when the insert succeeds, false otherwise.
     */
    public function save(): bool
    {
        $data = [];
        foreach ($this->attributes() as $attribute) {
            $data[$attribute] = $this->{$attribute};
        }

        return QueryBuilder::table($this->tableName())->insert($data);
    }

    /**
     * Soft-deletes the current record.
     *
     * Sets the `deleted_at` column of the record matching this model's id to
     * the current timestamp, and returns the real execution result.
     *
     * @return bool True when the update succeeds, false otherwise.
     */
    public function delete() : bool
    {
        $tableName = $this->tableName();

        // `deleted_at = NOW()` is a constant SQL expression (no user value), so
        // it is set via a raw statement while the id stays param-bound.
        $statement = self::prepare("UPDATE $tableName SET deleted_at = NOW() WHERE id = :id");
        $statement->bindValue(':id', $this->id);

        return $statement->execute();
    }

    /**
     * Updates the current record in the database.
     *
     * Sets every model attribute on the row matching this model's id using the
     * param-bound {@see QueryBuilder}, and returns the real execution result.
     *
     * @return bool True when the update succeeds, false otherwise.
     */
    public function update() : bool
    {
        $data = [];
        foreach ($this->attributes() as $attribute) {
            $data[$attribute] = $this->{$attribute};
        }

        return QueryBuilder::table($this->tableName())
            ->where('id', $this->id)
            ->update($data);
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
     * Finds the first record matching the given equality conditions.
     *
     * Conditions are joined with ` AND ` and every value is bound as a
     * parameter, so multi-condition lookups such as
     * `findOne(['a' => 1, 'b' => 2])` produce valid, injection-safe SQL.
     *
     * @param array<string, mixed> $where Equality conditions (column => value).
     * @return bool|object The hydrated model on a hit, false when none match.
     */
    public static function findOne(array $where): bool|object
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(' AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    /**
     * Finds a single record by its primary key.
     *
     * @param mixed $id The primary key value.
     * @return object|null The hydrated model, or null when not found.
     */
    public static function find(mixed $id): ?object
    {
        $record = static::findOne([static::primaryKey() => $id]);
        return $record === false ? null : $record;
    }

    /**
     * Returns all records matching the given equality conditions.
     *
     * Every value is bound as a parameter via {@see QueryBuilder}; results are
     * hydrated into instances of the calling model class.
     *
     * @param array<string, mixed> $where Equality conditions (column => value).
     * @return array<int, object> The hydrated models (empty when none match).
     */
    public static function where(array $where = []): array
    {
        $builder = QueryBuilder::table(static::tableName());
        foreach ($where as $column => $value) {
            $builder->where($column, $value);
        }

        $statement = self::prepare($builder->toSql());
        foreach ($builder->getParams() as $name => $value) {
            $statement->bindValue(":$name", $value);
        }
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    /**
     * Returns every record in the model's table.
     *
     * @return array<int, object> The hydrated models.
     */
    public static function findAll(): array
    {
        return static::where();
    }
}
