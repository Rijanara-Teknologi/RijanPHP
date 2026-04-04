<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Database\QueryBuilder;
use Teguh02\Rijanphp\Core\Database\Connection;

/**
 * Tests for Bug #6: QueryBuilder where() with null value produces invalid parameter count.
 *
 * When where('column', null) is called, the fix generates "column IS NULL"
 * instead of "column = ?" to avoid a placeholder/binding mismatch with PDO.
 *
 * All tests in this file work without a live database by inspecting the
 * compiled SQL and bindings directly via a test-accessible subclass.
 */
class QueryBuilderTest extends TestCase
{
    private QueryBuilder $qb;

    protected function setUp(): void
    {
        // Use a minimal stub connection — tests inspect SQL only, no real DB calls
        $connection = $this->createMockConnection();
        $this->qb = new class($connection) extends QueryBuilder {
            public function exposeSql(): string
            {
                return $this->compileSelect();
            }

            public function exposeBindings(): array
            {
                return $this->getBindings();
            }

            public function exposeWhere(): array
            {
                return $this->wheres;
            }
        };
        $this->qb->table('users');
    }

    private function createMockConnection(): Connection
    {
        return $this->createMock(Connection::class);
    }

    private function freshQb(): QueryBuilder
    {
        $connection = $this->createMockConnection();
        $qb = new class($connection) extends QueryBuilder {
            public function exposeSql(): string
            {
                return $this->compileSelect();
            }

            public function exposeBindings(): array
            {
                return $this->getBindings();
            }

            public function exposeWhere(): array
            {
                return $this->wheres;
            }
        };
        $qb->table('users');
        return $qb;
    }

    // -------------------------------------------------------------------------
    // Bug #6: where(col, null) uses IS NULL, NOT "= ?"
    // -------------------------------------------------------------------------

    public function testWhereNullProducesIsNull()
    {
        $qb = $this->freshQb();
        $qb->where('deleted_at', null);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('deleted_at IS NULL', $sql);
        $this->assertStringNotContainsString('deleted_at = ?', $sql);
    }

    public function testWhereNullProducesNoBindings()
    {
        $qb = $this->freshQb();
        $qb->where('deleted_at', null);

        $this->assertCount(0, $qb->exposeBindings());
    }

    public function testOrWhereNullProducesIsNull()
    {
        $qb = $this->freshQb();
        $qb->where('status', 'active')->orWhere('deleted_at', null);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('deleted_at IS NULL', $sql);
        $this->assertStringNotContainsString('deleted_at = ?', $sql);
    }

    public function testOrWhereNullProducesOnlyOneBinding()
    {
        $qb = $this->freshQb();
        $qb->where('status', 'active')->orWhere('deleted_at', null);

        // Only the 'status = ?' binding should exist; null generates no binding
        $bindings = $qb->exposeBindings();
        $this->assertCount(1, $bindings);
        $this->assertSame('active', $bindings[0]);
    }

    public function testWhereNullWithExplicitEqualsOperatorStillUsesIsNull()
    {
        // Calling where('col', '=', null) should also produce IS NULL
        $qb = $this->freshQb();
        $qb->where('deleted_at', '=', null);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('deleted_at IS NULL', $sql);
        $this->assertCount(0, $qb->exposeBindings());
    }

    // -------------------------------------------------------------------------
    // Regression: where() with non-null values still works correctly
    // -------------------------------------------------------------------------

    public function testWhereWithStringValue()
    {
        $qb = $this->freshQb();
        $qb->where('email', 'test@example.com');

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('email = ?', $sql);

        $bindings = $qb->exposeBindings();
        $this->assertCount(1, $bindings);
        $this->assertSame('test@example.com', $bindings[0]);
    }

    public function testWhereWithOperatorAndValue()
    {
        $qb = $this->freshQb();
        $qb->where('age', '>', 18);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('age > ?', $sql);
        $this->assertSame([18], $qb->exposeBindings());
    }

    public function testMultipleWheresProduceCorrectBindingCount()
    {
        $qb = $this->freshQb();
        $qb->where('name', 'Alice')
           ->where('deleted_at', null)
           ->where('role', 'admin');

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('name = ?', $sql);
        $this->assertStringContainsString('deleted_at IS NULL', $sql);
        $this->assertStringContainsString('role = ?', $sql);

        // Only 'Alice' and 'admin' should be bound; null generates no binding
        $bindings = $qb->exposeBindings();
        $this->assertCount(2, $bindings);
        $this->assertSame('Alice', $bindings[0]);
        $this->assertSame('admin', $bindings[1]);
    }

    // -------------------------------------------------------------------------
    // whereNull / whereNotNull dedicated methods (existing, regression)
    // -------------------------------------------------------------------------

    public function testWhereNullMethod()
    {
        $qb = $this->freshQb();
        $qb->whereNull('deleted_at');

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('deleted_at IS NULL', $sql);
        $this->assertCount(0, $qb->exposeBindings());
    }

    public function testWhereNotNullMethod()
    {
        $qb = $this->freshQb();
        $qb->whereNotNull('verified_at');

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('verified_at IS NOT NULL', $sql);
        $this->assertCount(0, $qb->exposeBindings());
    }

    // -------------------------------------------------------------------------
    // whereIn (regression)
    // -------------------------------------------------------------------------

    public function testWhereIn()
    {
        $qb = $this->freshQb();
        $qb->whereIn('status', ['active', 'pending']);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('status IN (?, ?)', $sql);
        $this->assertCount(2, $qb->exposeBindings());
    }

    public function testWhereInEmptyArrayProducesAlwaysFalse()
    {
        $qb = $this->freshQb();
        $qb->whereIn('status', []);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('1 = 0', $sql);
    }

    // -------------------------------------------------------------------------
    // select / orderBy / limit (regression)
    // -------------------------------------------------------------------------

    public function testSelect()
    {
        $qb = $this->freshQb();
        $qb->select(['id', 'name']);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('SELECT id, name', $sql);
    }

    public function testOrderByAsc()
    {
        $qb = $this->freshQb();
        $qb->orderBy('created_at');

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('ORDER BY created_at ASC', $sql);
    }

    public function testLimitOffset()
    {
        $qb = $this->freshQb();
        $qb->limit(10, 20);

        $sql = $qb->exposeSql();
        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 20', $sql);
    }

    // -------------------------------------------------------------------------
    // Invalid operator/identifier protection
    // -------------------------------------------------------------------------

    public function testInvalidOperatorThrows()
    {
        $this->expectException(\InvalidArgumentException::class);
        $qb = $this->freshQb();
        $qb->where('name', 'DROP TABLE', 'value');
    }

    public function testInvalidOrderDirectionThrows()
    {
        $this->expectException(\InvalidArgumentException::class);
        $qb = $this->freshQb();
        $qb->orderBy('id', 'SIDEWAYS');
    }
}
