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

    public function testOrderByInvalidDirection()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->orderBy('created_at', 'INVALID');
    }

    public function testOrderByRaw()
    {
        $this->builder->orderByRaw('FIELD(id, 3, 1, 2)')->get();
        $this->assertStringContainsString('ORDER BY FIELD(id, 3, 1, 2)', $this->getLastSql());
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
        $this->assertStringContainsString('MAX(age)', $this->mockConnection->lastFetchSql);
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

    public function testCount()
    {
        $count = $this->builder->count();
        $this->assertIsInt($count);
    }

    public function testSum()
    {
        $sum = $this->builder->sum('price');
        $this->assertNotNull($sum);
    }

    public function testAvg()
    {
        $avg = $this->builder->avg('price');
        $this->assertNotNull($avg);
    }

    public function testMin()
    {
        $min = $this->builder->min('price');
        $this->assertNotNull($min);
    }

    public function testWhereIn()
    {
        $this->builder->whereIn('id', [1, 2, 3])->get();
        $this->assertStringContainsString('IN (?, ?, ?)', $this->getLastSql());
    }

    public function testWhereNull()
    {
        $this->builder->whereNull('deleted_at')->get();
        $this->assertStringContainsString('IS NULL', $this->getLastSql());
    }

    public function testWhereNotNull()
    {
        $this->builder->whereNotNull('email')->get();
        $this->assertStringContainsString('IS NOT NULL', $this->getLastSql());
    }

    public function testLatest()
    {
        $this->builder->latest('created_at')->get();
        $this->assertStringContainsString('ORDER BY created_at DESC', $this->getLastSql());
    }

    public function testOldest()
    {
        $this->builder->oldest('created_at')->get();
        $this->assertStringContainsString('ORDER BY created_at ASC', $this->getLastSql());
    }

    public function testValue()
    {
        $value = $this->builder->value('name');
        $this->assertNotNull($value);
    }

    public function testDoesntExist()
    {
        $this->assertFalse($this->builder->doesntExist());
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

    public function testInvalidOperator()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->where('id', 'INVALID_OP', 1)->get();
    }

    public function testInvalidJoinType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->join('posts', 'users.id', '=', 'posts.user_id', 'INVALID')->get();
    }

    public function testInvalidIdentifier()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->select(['id; DROP TABLE users'])->get();
    }

    public function testReset()
    {
        $this->builder->where('id', 1)->orderBy('name')->limit(10);
        $this->builder->reset();
        $this->builder->get();
        $this->assertStringContainsString('SELECT * FROM users', $this->getLastSql());
        $this->assertStringNotContainsString('WHERE', $this->getLastSql());
        $this->assertStringNotContainsString('ORDER BY', $this->getLastSql());
        $this->assertStringNotContainsString('LIMIT', $this->getLastSql());
    }

    public function testOrWhere()
    {
        $this->builder->where('status', 'active')->orWhere('status', 'pending')->get();
        $sql = $this->getLastSql();
        $this->assertStringContainsString('OR', $sql);
    }

    public function testChunk()
    {
        $results = [];
        $this->builder->chunk(10, function($chunk, $page) use (&$results) {
            $results[$page] = $chunk;
            return true;
        });
        $this->assertNotEmpty($results);
    }
}
