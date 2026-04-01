<?php

namespace Teguh02\Rijanphp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Database\QueryBuilder;
use Teguh02\Rijanphp\Core\Database\Connection;

class QueryBuilderTest extends TestCase
{
    protected $builder;
    protected $mockConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConnection = new class implements Connection {
            public $lastQuery = '';
            public $lastBindings = [];
            public $lastFetchAllSql = '';
            public $lastFetchSql = '';

            public function connect(array $config) { return $this; }

            public function query($sql, $bindings = []) {
                $this->lastQuery = $sql;
                $this->lastBindings = $bindings;
                return new class {
                    public function rowCount() { return 1; }
                    public function fetchAll($mode = null) { return [['id' => 1, 'name' => 'Test']]; }
                    public function fetch($mode = null) { return ['id' => 1, 'name' => 'Test']; }
                };
            }

            public function fetch($sql, $bindings = []) {
                $this->lastFetchSql = $sql;
                $this->lastQuery = $sql;
                $this->lastBindings = $bindings;
                return ['id' => 1, 'name' => 'Test'];
            }

            public function fetchAll($sql, $bindings = []) {
                $this->lastFetchAllSql = $sql;
                $this->lastQuery = $sql;
                $this->lastBindings = $bindings;
                return [['id' => 1, 'name' => 'Test']];
            }

            public function lastInsertId() { return 42; }
            public function beginTransaction() { return true; }
            public function commit() { return true; }
            public function rollBack() { return true; }
        };

        $this->builder = new QueryBuilder($this->mockConnection);
        $this->builder->table('users');
    }

    protected function getLastSql()
    {
        return $this->mockConnection->lastFetchAllSql ?: $this->mockConnection->lastFetchSql ?: $this->mockConnection->lastQuery;
    }

    public function testSelectAll()
    {
        $this->builder->get();
        $this->assertStringContainsString('SELECT * FROM users', $this->getLastSql());
    }

    public function testSelectSpecificColumns()
    {
        $this->builder->select(['id', 'name'])->get();
        $this->assertStringContainsString('SELECT id, name FROM users', $this->getLastSql());
    }

    public function testWhereClause()
    {
        $this->builder->where('status', 'active')->get();
        $this->assertStringContainsString("WHERE status = ?", $this->getLastSql());
        $this->assertContains('active', $this->mockConnection->lastBindings);
    }

    public function testMultipleWhereClauses()
    {
        $this->builder->where('status', 'active')->where('age', '>', 18)->get();
        $this->assertStringContainsString('AND', $this->getLastSql());
        $this->assertCount(2, $this->mockConnection->lastBindings);
    }

    public function testOrderBy()
    {
        $this->builder->orderBy('created_at', 'DESC')->get();
        $this->assertStringContainsString('ORDER BY created_at DESC', $this->getLastSql());
    }

    public function testLimit()
    {
        $this->builder->limit(10)->get();
        $this->assertStringContainsString('LIMIT 10', $this->getLastSql());
    }

    public function testLimitWithOffset()
    {
        $this->builder->limit(10, 20)->get();
        $this->assertStringContainsString('LIMIT 10', $this->getLastSql());
        $this->assertStringContainsString('OFFSET 20', $this->getLastSql());
    }

    public function testInsertSingleRow()
    {
        $id = $this->builder->insert(['name' => 'John', 'email' => 'john@example.com']);
        $this->assertEquals(42, $id);
        $this->assertStringContainsString('INSERT INTO users', $this->mockConnection->lastQuery);
    }

    public function testInsertMultipleRows()
    {
        $data = [
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'Jane', 'email' => 'jane@example.com'],
        ];
        $result = $this->builder->insert($data);
        $this->assertTrue($result);
        $this->assertStringContainsString('(?, ?), (?, ?)', $this->mockConnection->lastQuery);
    }

    public function testUpdate()
    {
        $this->builder->where('id', 1);
        $result = $this->builder->update(['name' => 'Updated']);
        $this->assertStringContainsString('UPDATE users SET', $this->mockConnection->lastQuery);
        $this->assertStringContainsString('name = ?', $this->mockConnection->lastQuery);
    }

    public function testDelete()
    {
        $this->builder->where('id', 1);
        $result = $this->builder->delete();
        $this->assertStringContainsString('DELETE FROM users', $this->mockConnection->lastQuery);
    }

    public function testJoin()
    {
        $this->builder->join('posts', 'users.id', '=', 'posts.user_id')->get();
        $this->assertStringContainsString('INNER JOIN posts ON users.id = posts.user_id', $this->getLastSql());
    }

    public function testLeftJoin()
    {
        $this->builder->leftJoin('posts', 'users.id', '=', 'posts.user_id')->get();
        $this->assertStringContainsString('LEFT JOIN posts ON users.id = posts.user_id', $this->getLastSql());
    }

    public function testRightJoin()
    {
        $this->builder->rightJoin('posts', 'users.id', '=', 'posts.user_id')->get();
        $this->assertStringContainsString('RIGHT JOIN posts ON users.id = posts.user_id', $this->getLastSql());
    }

    public function testPluck()
    {
        $results = $this->builder->pluck('name');
        $this->assertIsArray($results);
    }

    public function testMax()
    {
        $max = $this->builder->max('age');
        $this->assertStringContainsString('MAX(age)', $this->mockConnection->lastQuery);
    }

    public function testFirst()
    {
        $result = $this->builder->first();
        $this->assertIsArray($result);
        $this->assertStringContainsString('LIMIT 1', $this->mockConnection->lastFetchSql);
    }

    public function testExists()
    {
        $exists = $this->builder->exists();
        $this->assertTrue($exists);
    }

    public function testComplexQuery()
    {
        $this->builder
            ->select(['id', 'name', 'email'])
            ->where('status', 'active')
            ->where('age', '>=', 18)
            ->orderBy('name', 'ASC')
            ->limit(10)
            ->get();

        $sql = $this->getLastSql();
        $this->assertStringContainsString('SELECT id, name, email FROM users', $sql);
        $this->assertStringContainsString('WHERE', $sql);
        $this->assertStringContainsString('ORDER BY', $sql);
        $this->assertStringContainsString('LIMIT', $sql);
    }
}
