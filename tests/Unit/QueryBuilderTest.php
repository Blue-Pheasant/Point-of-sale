<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Common\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the param-bound {@see QueryBuilder} (roadmap T05).
 *
 * These cover SQL generation and parameter binding only — execution against a
 * live database is exercised by integration tests (roadmap T18).
 */
final class QueryBuilderTest extends TestCase
{
    public function testSelectAllWithoutConditions(): void
    {
        $builder = QueryBuilder::table('products');

        $this->assertSame('SELECT * FROM products', $builder->toSql());
        $this->assertSame([], $builder->getParams());
    }

    public function testSelectSpecificColumns(): void
    {
        $builder = QueryBuilder::table('products')->select(['id', 'name']);

        $this->assertSame('SELECT id, name FROM products', $builder->toSql());
    }

    public function testWhereBindsValueAsParameter(): void
    {
        $builder = QueryBuilder::table('products')->where('category_id', 7);

        $this->assertSame('SELECT * FROM products WHERE category_id = :p0', $builder->toSql());
        $this->assertSame(['p0' => 7], $builder->getParams());
    }

    public function testMultipleWheresAreJoinedWithAnd(): void
    {
        $builder = QueryBuilder::table('users')
            ->where('role_id', 2)
            ->where('active', 1);

        $this->assertSame(
            'SELECT * FROM users WHERE role_id = :p0 AND active = :p1',
            $builder->toSql()
        );
        $this->assertSame(['p0' => 2, 'p1' => 1], $builder->getParams());
    }

    public function testWhereLikeWrapsValueInWildcardsAndBinds(): void
    {
        $builder = QueryBuilder::table('products')->whereLike('name', 'coffee');

        $this->assertSame('SELECT * FROM products WHERE name LIKE :p0', $builder->toSql());
        $this->assertSame(['p0' => '%coffee%'], $builder->getParams());
    }

    public function testOrderByValidatesDirection(): void
    {
        $builder = QueryBuilder::table('products')->orderBy('created_at', 'drop table');

        // An unrecognised direction falls back to ASC; it can never inject SQL.
        $this->assertSame('SELECT * FROM products ORDER BY created_at ASC', $builder->toSql());
    }

    public function testLimitAndOffsetAreBound(): void
    {
        $builder = QueryBuilder::table('products')->limit(10)->offset(20);

        $this->assertSame('SELECT * FROM products LIMIT :limit OFFSET :offset', $builder->toSql());
        $this->assertSame(['limit' => 10, 'offset' => 20], $builder->getParams());
    }

    public function testInjectionAttemptIsTreatedAsAParameterValue(): void
    {
        $builder = QueryBuilder::table('users')->where('email', "' OR '1'='1");

        // The malicious string never reaches the SQL — only a placeholder does.
        $this->assertSame('SELECT * FROM users WHERE email = :p0', $builder->toSql());
        $this->assertSame(['p0' => "' OR '1'='1"], $builder->getParams());
    }

    public function testFullQueryComposition(): void
    {
        $builder = QueryBuilder::table('products')
            ->select(['id', 'name'])
            ->where('category_id', 3)
            ->whereLike('name', 'tea')
            ->orderBy('price', 'DESC')
            ->limit(5)
            ->offset(10);

        $this->assertSame(
            'SELECT id, name FROM products WHERE category_id = :p0 AND name LIKE :p1'
            . ' ORDER BY price DESC LIMIT :limit OFFSET :offset',
            $builder->toSql()
        );
        $this->assertSame(
            ['p0' => 3, 'p1' => '%tea%', 'limit' => 5, 'offset' => 10],
            $builder->getParams()
        );
    }
}
