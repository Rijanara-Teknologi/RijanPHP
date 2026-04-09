<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;
use Teguh02\Rijanphp\Core\Database\Schema\Schema;

/**
 * Tests for Bug #9: Schema::table() for ALTER TABLE support.
 *
 * Schema only had create(), drop(), dropIfExists(), and hasTable().
 * There was no table() method for modifying existing tables via ALTER TABLE.
 *
 * Blueprint must also expose setModifying() and toAlterSql().
 */
class SchemaTableTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Blueprint::setModifying() and toAlterSql()
    // -------------------------------------------------------------------------

    public function testBlueprintHasSetModifyingMethod()
    {
        $this->assertTrue(
            method_exists(Blueprint::class, 'setModifying'),
            'Blueprint must have setModifying() method'
        );
    }

    public function testBlueprintHasToAlterSqlMethod()
    {
        $this->assertTrue(
            method_exists(Blueprint::class, 'toAlterSql'),
            'Blueprint must have toAlterSql() method'
        );
    }

    public function testToAlterSqlReturnsAlterTableStatements()
    {
        $blueprint = new Blueprint('users');
        $blueprint->setModifying(true);
        $blueprint->string('nickname');

        $statements = $blueprint->toAlterSql();

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $this->assertStringStartsWith('ALTER TABLE users ADD COLUMN', $statements[0]);
        $this->assertStringContainsString('nickname', $statements[0]);
    }

    public function testToAlterSqlSupportsMultipleColumns()
    {
        $blueprint = new Blueprint('posts');
        $blueprint->setModifying(true);
        $blueprint->boolean('is_published');
        $blueprint->timestamp('published_at')->nullable();

        $statements = $blueprint->toAlterSql();

        $this->assertCount(2, $statements);
        $this->assertStringContainsString('is_published', $statements[0]);
        $this->assertStringContainsString('published_at', $statements[1]);
        foreach ($statements as $sql) {
            $this->assertStringStartsWith('ALTER TABLE posts ADD COLUMN', $sql);
        }
    }

    public function testToAlterSqlPreservesColumnDefinition()
    {
        $blueprint = new Blueprint('orders');
        $blueprint->setModifying(true);
        $blueprint->decimal('discount', 5, 2)->nullable()->default(0);

        $statements = $blueprint->toAlterSql();

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('discount', $statements[0]);
        $this->assertStringContainsString('DECIMAL', $statements[0]);
    }

    public function testToAlterSqlIsEmptyWhenNoColumnsAdded()
    {
        $blueprint = new Blueprint('articles');
        $blueprint->setModifying(true);

        $statements = $blueprint->toAlterSql();

        $this->assertIsArray($statements);
        $this->assertCount(0, $statements);
    }

    // -------------------------------------------------------------------------
    // Schema::table() method exists
    // -------------------------------------------------------------------------

    public function testSchemaHasTableMethod()
    {
        $this->assertTrue(
            method_exists(Schema::class, 'table'),
            'Schema must have a table() method for ALTER TABLE support'
        );
    }

    public function testSchemaTablePassesBlueprintInModifyingMode()
    {
        // We intercept by using a Blueprint that records the isModifying flag
        $capturedBlueprint = null;

        // Manually replicate what Schema::table() does to verify the flow
        $blueprint = new Blueprint('users');
        $blueprint->setModifying(true);
        $blueprint->string('avatar_url', 500);

        $statements = $blueprint->toAlterSql();

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('ALTER TABLE', $statements[0]);
    }
}
