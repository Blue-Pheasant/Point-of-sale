<?php

namespace app\Common;

/**
 * Class QueryBuilder
 *
 * A small fluent query builder that ALWAYS binds values as parameters.
 *
 * Every value passed through {@see self::where()}, {@see self::whereLike()},
 * {@see self::insert()} or {@see self::update()} is turned into a uniquely
 * named placeholder (`:p0`, `:p1`, ...) and bound through {@see Query}, which
 * uses real (non-emulated) prepared statements. No caller value is ever
 * concatenated into the SQL string, so the builder is safe against SQL
 * injection by construction.
 *
 * Usage:
 *   QueryBuilder::table('products')
 *       ->select(['id', 'name'])
 *       ->where('category_id', $categoryId)
 *       ->whereLike('name', $keyword)
 *       ->orderBy('created_at', 'DESC')
 *       ->limit(10)->offset(20)
 *       ->get();
 *
 * @package app\Common
 */
class QueryBuilder
{
    /**
     * @var string The table the query operates on.
     */
    private string $table;

    /**
     * @var array<int, string> The selected columns. Empty means `*`.
     */
    private array $columns = [];

    /**
     * @var array<int, string> Raw WHERE fragments (each already param-bound).
     */
    private array $wheres = [];

    /**
     * @var array<int, string> ORDER BY fragments.
     */
    private array $orders = [];

    /**
     * @var int|null The LIMIT, or null for none.
     */
    private ?int $limit = null;

    /**
     * @var int|null The OFFSET, or null for none.
     */
    private ?int $offset = null;

    /**
     * @var array<string, mixed> Bound parameters keyed by placeholder name.
     */
    private array $params = [];

    /**
     * @var int Counter used to generate unique placeholder names.
     */
    private int $placeholderCount = 0;

    /**
     * QueryBuilder constructor.
     *
     * @param string $table The table to operate on.
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Creates a new builder for the given table.
     *
     * @param string $table The table to operate on.
     * @return self
     */
    public static function table(string $table): self
    {
        return new self($table);
    }

    /**
     * Sets the columns to select.
     *
     * @param array<int, string> $columns The columns to select. Empty for `*`.
     * @return self
     */
    public function select(array $columns = []): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Adds an equality WHERE condition.
     *
     * @param string $column The column to compare.
     * @param mixed $value The value to bind.
     * @return self
     */
    public function where(string $column, mixed $value): self
    {
        $placeholder = $this->bind($value);
        $this->wheres[] = "$column = :$placeholder";
        return $this;
    }

    /**
     * Adds a LIKE WHERE condition. The `%` wildcards are added around the
     * value here, and the whole pattern is bound as a single parameter.
     *
     * @param string $column The column to match.
     * @param string $value The value to wrap in wildcards and bind.
     * @return self
     */
    public function whereLike(string $column, string $value): self
    {
        $placeholder = $this->bind("%$value%");
        $this->wheres[] = "$column LIKE :$placeholder";
        return $this;
    }

    /**
     * Adds a `column >= value` WHERE condition with the value bound.
     *
     * @param string $column The column to compare.
     * @param mixed $value The lower bound to bind.
     * @return self
     */
    public function whereGte(string $column, mixed $value): self
    {
        $placeholder = $this->bind($value);
        $this->wheres[] = "$column >= :$placeholder";
        return $this;
    }

    /**
     * Adds a `column <= value` WHERE condition with the value bound.
     *
     * @param string $column The column to compare.
     * @param mixed $value The upper bound to bind.
     * @return self
     */
    public function whereLte(string $column, mixed $value): self
    {
        $placeholder = $this->bind($value);
        $this->wheres[] = "$column <= :$placeholder";
        return $this;
    }

    /**
     * Adds a raw WHERE fragment with optional bound parameters. Use only for
     * fragments that contain no caller-supplied values, or pass values through
     * the $params map so they are bound (never concatenated).
     *
     * @param string $expression The SQL fragment (e.g. `deleted_at IS NULL`).
     * @param array<string, mixed> $params Optional named params used by $expression.
     * @return self
     */
    public function whereRaw(string $expression, array $params = []): self
    {
        $this->wheres[] = $expression;
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }
        return $this;
    }

    /**
     * Adds an ORDER BY clause. The direction is validated to ASC/DESC so it
     * can never carry an injected fragment.
     *
     * @param string $column The column to order by.
     * @param string $direction The sort direction (ASC or DESC).
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = "$column $direction";
        return $this;
    }

    /**
     * Sets the LIMIT.
     *
     * @param int $limit The maximum number of rows to return.
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Sets the OFFSET.
     *
     * @param int $offset The number of rows to skip.
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Builds the SELECT SQL string. Values live in {@see self::getParams()},
     * never in the returned SQL.
     *
     * @return string The SELECT statement with placeholders.
     */
    public function toSql(): string
    {
        $columns = count($this->columns) > 0 ? implode(', ', $this->columns) : '*';
        $sql = "SELECT $columns FROM {$this->table}";

        if (count($this->wheres) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if (count($this->orders) > 0) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        // LIMIT/OFFSET are bound as integer parameters so they pass cleanly
        // under PDO::ATTR_EMULATE_PREPARES = false.
        if ($this->limit !== null) {
            $sql .= ' LIMIT :limit';
            $this->params['limit'] = $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET :offset';
            $this->params['offset'] = $this->offset;
        }

        return $sql;
    }

    /**
     * Returns the bound parameters collected while building the query.
     *
     * @return array<string, mixed> The parameter map.
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Executes the SELECT and returns all matching rows.
     *
     * @return array<int, array<string, mixed>> The fetched rows.
     */
    public function get(): array
    {
        $sql = $this->toSql();
        return Query::getAll($sql, $this->params);
    }

    /**
     * Executes the SELECT and returns the first matching row, or null.
     *
     * @return array<string, mixed>|null The first row, or null when empty.
     */
    public function first(): ?array
    {
        $this->limit(1);
        $rows = $this->get();
        return $rows[0] ?? null;
    }

    /**
     * Returns the number of rows matching the current WHERE conditions.
     *
     * @return int The row count.
     */
    public function count(): int
    {
        // Reuse the WHERE/params built so far, ignoring select/limit/offset.
        $sql = "SELECT * FROM {$this->table}";
        if (count($this->wheres) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $params = $this->params;
        unset($params['limit'], $params['offset']);

        return Query::getCount($sql, $params);
    }

    /**
     * Inserts a row and returns whether the statement succeeded.
     *
     * @param array<string, mixed> $data Column => value pairs to insert.
     * @return bool True on success, false otherwise.
     */
    public function insert(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = [];
        foreach ($data as $value) {
            $placeholders[] = ':' . $this->bind($value);
        }

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ')'
            . ' VALUES (' . implode(', ', $placeholders) . ')';

        $statement = Query::prepare($sql, $this->params);
        return $statement->execute();
    }

    /**
     * Updates rows matching the current WHERE conditions.
     *
     * @param array<string, mixed> $data Column => value pairs to set.
     * @return bool True on success, false otherwise.
     */
    public function update(array $data): bool
    {
        $set = [];
        foreach ($data as $column => $value) {
            $placeholder = $this->bind($value);
            $set[] = "$column = :$placeholder";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        if (count($this->wheres) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $statement = Query::prepare($sql, $this->params);
        return $statement->execute();
    }

    /**
     * Deletes rows matching the current WHERE conditions.
     *
     * @return bool True on success, false otherwise.
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        if (count($this->wheres) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $statement = Query::prepare($sql, $this->params);
        return $statement->execute();
    }

    /**
     * Registers a value under a fresh, unique placeholder name and returns it.
     *
     * @param mixed $value The value to bind.
     * @return string The generated placeholder name (without the leading colon).
     */
    private function bind(mixed $value): string
    {
        $name = 'p' . $this->placeholderCount++;
        $this->params[$name] = $value;
        return $name;
    }
}
