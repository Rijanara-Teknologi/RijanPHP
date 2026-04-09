<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Database\QueryBuilder;
use Teguh02\Rijanphp\Core\Database\Connection;

/**
 * Tests for Bug #10: QueryBuilder aggregate() returning wrong values due to
 * using first() internally (which sets LIMIT 1) and not restoring state.
 *
 * After the fix, aggregate() must:
 *  - Not modify $this->limit or $this->offset permanently
 *  - Use get() instead of first() to avoid injecting LIMIT 1 into aggregate SQL
 *  - Return 0 (not null) for COUNT when the result set is empty
 */
class AggregateTest extends TestCase
{
    private function makeQb(): QueryBuilder
    {
        $connection = $this->createMock(Connection::class);

        return new class($connection) extends QueryBuilder {
            public function exposeSql(): string
            {
                return $this->compileSelect();
            }

            public function exposeLimit(): ?int
            {
                return $this->limit;
            }

            public function exposeOffset(): ?int
            {
                return $this->offset;
            }

            public function exposeSelect(): string
            {
                return $this->select;
            }

            /**
             * Override get() to return a controlled result for aggregate tests.
             * @var array|null
             */
            public $fakeGetResult = null;

            public function get()
            {
                if ($this->fakeGetResult !== null) {
                    return $this->fakeGetResult;
                }
                return [];
            }
        };
    }

    // -------------------------------------------------------------------------
    // aggregate() must not leave LIMIT 1 on the builder
    // -------------------------------------------------------------------------

    public function testAggregateSqlDoesNotContainLimit1()
    {
        $qb = $this->makeQb();
        $qb->table('services');

        // Simulate count() calling aggregate()
        $qb->fakeGetResult = [['aggregate' => 5]];
        $qb->count();

        // After aggregate(), LIMIT should not be set
        $this->assertNull($qb->exposeLimit());
    }

    public function testAggregateRestoresLimitAfterExecution()
    {
        $qb = $this->makeQb();
        $qb->table('services');
        $qb->limit(10);

        $qb->fakeGetResult = [['aggregate' => 3]];
        $qb->count();

        // Limit should be restored to original value
        $this->assertSame(10, $qb->exposeLimit());
    }

    public function testAggregateRestoresOffsetAfterExecution()
    {
        $qb = $this->makeQb();
        $qb->table('services');
        $qb->limit(10, 20); // offset = 20

        $qb->fakeGetResult = [['aggregate' => 7]];
        $qb->count();

        $this->assertSame(20, $qb->exposeOffset());
    }

    public function testAggregateRestoresSelectAfterExecution()
    {
        $qb = $this->makeQb();
        $qb->table('services');
        $qb->select(['id', 'name']);

        $qb->fakeGetResult = [['aggregate' => 2]];
        $qb->count();

        $this->assertStringNotContainsString('aggregate', $qb->exposeSelect());
        $this->assertStringContainsString('id', $qb->exposeSelect());
    }

    // -------------------------------------------------------------------------
    // COUNT returns 0 when no rows found
    // -------------------------------------------------------------------------

    public function testCountReturnsZeroWhenNoRows()
    {
        $qb = $this->makeQb();
        $qb->table('services');
        $qb->fakeGetResult = [];

        $result = $qb->count();

        $this->assertSame(0, $result);
    }

    public function testCountReturnsZeroForEmptyResult()
    {
        $qb = $this->makeQb();
        $qb->table('services');
        $qb->fakeGetResult = []; // empty — no rows matched

        $this->assertSame(0, $qb->count());
    }

    // -------------------------------------------------------------------------
    // Aggregate returns correct value from result
    // -------------------------------------------------------------------------

    public function testCountReturnsCorrectValue()
    {
        $qb = $this->makeQb();
        $qb->table('tickets');
        $qb->fakeGetResult = [['aggregate' => 42]];

        $this->assertSame(42, $qb->count());
    }

    public function testSumReturnsNullWhenNoRows()
    {
        $qb = $this->makeQb();
        $qb->table('invoices');
        $qb->fakeGetResult = [];

        // sum() returns null (not 0) for empty results — only count() returns 0
        $this->assertNull($qb->sum('amount'));
    }

    public function testSumReturnsCorrectValue()
    {
        $qb = $this->makeQb();
        $qb->table('invoices');
        $qb->fakeGetResult = [['aggregate' => '1500.00']];

        $this->assertEquals('1500.00', $qb->sum('amount'));
    }
}
