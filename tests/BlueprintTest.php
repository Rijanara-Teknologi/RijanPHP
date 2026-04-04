<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;
use Teguh02\Rijanphp\Core\Database\Schema\BlueprintForeignKey;

/**
 * Tests for Bug #1: Blueprint Schema missing methods.
 *
 * Verifies all column types, fluent modifiers, softDeletes, and foreign keys
 * produce correct SQL fragments.
 */
class BlueprintTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Existing methods regression
    // -------------------------------------------------------------------------

    public function testIdColumnSqlite()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('users');
        $bp->id();
        $this->assertStringContainsString('id INTEGER PRIMARY KEY AUTOINCREMENT', $bp->toSql());
    }

    public function testIdColumnMysql()
    {
        putenv('DB_CONNECTION=mysql');
        $bp = new Blueprint('users');
        $bp->id();
        $this->assertStringContainsString('id INT PRIMARY KEY AUTO_INCREMENT', $bp->toSql());
        putenv('DB_CONNECTION=');
    }

    public function testIdColumnPgsql()
    {
        putenv('DB_CONNECTION=pgsql');
        $bp = new Blueprint('users');
        $bp->id();
        $this->assertStringContainsString('id SERIAL PRIMARY KEY', $bp->toSql());
        putenv('DB_CONNECTION=');
    }

    public function testStringColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->string('name');
        $this->assertStringContainsString('name VARCHAR(255)', $bp->toSql());
    }

    public function testStringColumnCustomLength()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->string('code', 10);
        $this->assertStringContainsString('code VARCHAR(10)', $bp->toSql());
    }

    public function testIntegerColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->integer('age');
        $this->assertStringContainsString('age INTEGER', $bp->toSql());
    }

    public function testTextColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->text('body');
        $this->assertStringContainsString('body TEXT', $bp->toSql());
    }

    public function testTimestamps()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->timestamps();
        $sql = $bp->toSql();
        $this->assertStringContainsString('created_at DATETIME', $sql);
        $this->assertStringContainsString('updated_at DATETIME', $sql);
    }

    public function testRaw()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->raw('custom_col MEDIUMTEXT NOT NULL');
        $this->assertStringContainsString('custom_col MEDIUMTEXT NOT NULL', $bp->toSql());
    }

    // -------------------------------------------------------------------------
    // New column types (Bug #1)
    // -------------------------------------------------------------------------

    public function testBigIntegerColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->bigInteger('views');
        $this->assertStringContainsString('views BIGINT', $bp->toSql());
    }

    public function testUnsignedBigIntegerMysql()
    {
        putenv('DB_CONNECTION=mysql');
        $bp = new Blueprint('t');
        $bp->unsignedBigInteger('user_id');
        $this->assertStringContainsString('user_id BIGINT UNSIGNED', $bp->toSql());
        putenv('DB_CONNECTION=');
    }

    public function testUnsignedBigIntegerSqlite()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->unsignedBigInteger('user_id');
        $this->assertStringContainsString('user_id BIGINT', $bp->toSql());
    }

    public function testBooleanColumnSqlite()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->boolean('active');
        $this->assertStringContainsString('active TINYINT(1)', $bp->toSql());
    }

    public function testBooleanColumnPgsql()
    {
        putenv('DB_CONNECTION=pgsql');
        $bp = new Blueprint('t');
        $bp->boolean('active');
        $this->assertStringContainsString('active BOOLEAN', $bp->toSql());
        putenv('DB_CONNECTION=');
    }

    public function testDecimalColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->decimal('price', 10, 4);
        $this->assertStringContainsString('price DECIMAL(10,4)', $bp->toSql());
    }

    public function testDecimalColumnDefaults()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->decimal('amount');
        $this->assertStringContainsString('amount DECIMAL(8,2)', $bp->toSql());
    }

    public function testFloatColumnSqlite()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->float('rate');
        $this->assertStringContainsString('rate REAL', $bp->toSql());
    }

    public function testTimestampColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->timestamp('verified_at');
        $this->assertStringContainsString('verified_at DATETIME', $bp->toSql());
    }

    public function testDateColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->date('birth_date');
        $this->assertStringContainsString('birth_date DATE', $bp->toSql());
    }

    public function testJsonColumnSqlite()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->json('meta');
        $this->assertStringContainsString('meta TEXT', $bp->toSql());
    }

    public function testJsonColumnMysql()
    {
        putenv('DB_CONNECTION=mysql');
        $bp = new Blueprint('t');
        $bp->json('meta');
        $this->assertStringContainsString('meta JSON', $bp->toSql());
        putenv('DB_CONNECTION=');
    }

    // -------------------------------------------------------------------------
    // Fluent modifiers (Bug #1)
    // -------------------------------------------------------------------------

    public function testNullableModifier()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->string('email')->nullable();
        $this->assertStringContainsString('email VARCHAR(255) NULL', $bp->toSql());
    }

    public function testUniqueModifier()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->string('email')->unique();
        $this->assertStringContainsString('email VARCHAR(255) UNIQUE', $bp->toSql());
    }

    public function testDefaultStringModifier()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->string('role')->default('user');
        $this->assertStringContainsString("role VARCHAR(255) DEFAULT 'user'", $bp->toSql());
    }

    public function testDefaultIntegerModifier()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->integer('status')->default(0);
        $this->assertStringContainsString('status INTEGER DEFAULT 0', $bp->toSql());
    }

    public function testDefaultBooleanModifier()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->boolean('active')->default(true);
        $this->assertStringContainsString('active TINYINT(1) DEFAULT 1', $bp->toSql());
    }

    public function testDefaultNullModifier()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->timestamp('deleted_at')->default(null);
        $this->assertStringContainsString('deleted_at DATETIME DEFAULT NULL', $bp->toSql());
    }

    public function testCombinedModifiers()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->string('email')->nullable()->unique();
        $sql = $bp->toSql();
        $this->assertStringContainsString('email VARCHAR(255) NULL UNIQUE', $sql);
    }

    // -------------------------------------------------------------------------
    // softDeletes (Bug #1)
    // -------------------------------------------------------------------------

    public function testSoftDeletesDefault()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->softDeletes();
        $this->assertStringContainsString('deleted_at DATETIME NULL DEFAULT NULL', $bp->toSql());
    }

    public function testSoftDeletesCustomColumn()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->softDeletes('removed_at');
        $this->assertStringContainsString('removed_at DATETIME NULL DEFAULT NULL', $bp->toSql());
    }

    // -------------------------------------------------------------------------
    // foreign() with BlueprintForeignKey (Bug #1)
    // -------------------------------------------------------------------------

    public function testForeignKeyFluentSyntax()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('posts');
        $bp->unsignedBigInteger('user_id');
        $result = $bp->foreign('user_id')->references('id')->on('users');

        // on() returns the Blueprint for chaining
        $this->assertInstanceOf(Blueprint::class, $result);

        $sql = $bp->toSql();
        $this->assertStringContainsString('FOREIGN KEY (user_id) REFERENCES users(id)', $sql);
    }

    public function testForeignKeyWithOnDelete()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('posts');
        $bp->unsignedBigInteger('user_id');
        $bp->foreign('user_id')->references('id')->onDelete('CASCADE')->on('users');

        $sql = $bp->toSql();
        $this->assertStringContainsString('ON DELETE CASCADE', $sql);
    }

    public function testForeignKeyReturnsBlueprintForeignKey()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $fk = $bp->foreign('user_id');
        $this->assertInstanceOf(BlueprintForeignKey::class, $fk);
    }

    // -------------------------------------------------------------------------
    // getColumns() accessor (Bug #1)
    // -------------------------------------------------------------------------

    public function testGetColumns()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('t');
        $bp->id()->string('name');
        $cols = $bp->getColumns();
        $this->assertCount(2, $cols);
    }

    // -------------------------------------------------------------------------
    // Full migration-style SQL (regression)
    // -------------------------------------------------------------------------

    public function testFullTableSql()
    {
        putenv('DB_CONNECTION=sqlite');
        $bp = new Blueprint('users');
        $bp->id()
           ->string('name')
           ->string('email')->unique()
           ->string('password')
           ->boolean('active')->default(true)
           ->softDeletes()
           ->timestamps();

        $sql = $bp->toSql();
        $this->assertStringStartsWith('CREATE TABLE IF NOT EXISTS users (', $sql);
        $this->assertStringContainsString('id INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $this->assertStringContainsString('email VARCHAR(255) UNIQUE', $sql);
        $this->assertStringContainsString('active TINYINT(1) DEFAULT 1', $sql);
        $this->assertStringContainsString('deleted_at DATETIME NULL DEFAULT NULL', $sql);
    }
}
