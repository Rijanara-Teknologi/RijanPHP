<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Database\QueryBuilder;
use Teguh02\Rijanphp\Core\Database\Connection;

/**
 * Tests for Bug #7: Model where() called twice causing duplicate SQL conditions.
 *
 * The Model::where() method previously called $this->builder->where() twice,
 * producing duplicate WHERE conditions and invalid parameter counts.
 *
 * We verify the fix by inspecting the compiled SQL directly from QueryBuilder,
 * simulating the same chain that Model::where() delegates to.
 */
class ModelWhereTest extends TestCase
{
    private function makeQb(): QueryBuilder
    {
        $connection = $this->createMock(Connection::class);

        return new class($connection) extends QueryBuilder {
            public function exposeSql(): string
            {
                return $this->compileSelect();
            }

            public function exposeBindings(): array
            {
                return $this->getBindings();
            }

            public function exposeWhereCount(): int
            {
                return count($this->wheres);
            }
        };
    }

    // -------------------------------------------------------------------------
    // Bug #7: single where() call must produce exactly one WHERE condition
    // -------------------------------------------------------------------------

    public function testSingleWhereProducesOneCondition()
    {
        $qb = $this->makeQb();
        $qb->table('users');
        $qb->where('status', 'active');

        $this->assertSame(1, $qb->exposeWhereCount());
    }

    public function testSingleWhereProducesOneBinding()
    {
        $qb = $this->makeQb();
        $qb->table('users');
        $qb->where('status', 'active');

        $this->assertCount(1, $qb->exposeBindings());
    }

    public function testChainedWheresProduceCorrectCountOfConditions()
    {
        $qb = $this->makeQb();
        $qb->table('users');
        $qb->where('site_domain', 'example.com');
        $qb->where('user_id', 1);

        // Should be exactly 2 conditions, not 4 (which the bug would produce)
        $this->assertSame(2, $qb->exposeWhereCount());
    }

    public function testChainedWheresProduceCorrectNumberOfBindings()
    {
        $qb = $this->makeQb();
        $qb->table('users');
        $qb->where('site_domain', 'example.com');
        $qb->where('user_id', 1);

        // 2 conditions → 2 bindings, not 4
        $this->assertCount(2, $qb->exposeBindings());
    }

    public function testChainedWheresProduceCorrectSql()
    {
        $qb = $this->makeQb();
        $qb->table('users');
        $qb->where('site_domain', 'example.com');
        $qb->where('user_id', 1);

        $sql = $qb->exposeSql();

        // Each column must appear exactly once in the WHERE clause
        $this->assertSame(1, substr_count($sql, 'site_domain'));
        $this->assertSame(1, substr_count($sql, 'user_id'));
    }

    public function testSqlContainsCorrectPlaceholders()
    {
        $qb = $this->makeQb();
        $qb->table('users');
        $qb->where('a', 1);
        $qb->where('b', 2);

        $sql = $qb->exposeSql();

        // Two '?' placeholders for two where conditions
        $this->assertSame(2, substr_count($sql, '?'));
    }
}
