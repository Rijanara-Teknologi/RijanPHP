<?php

namespace Teguh02\Rijanphp\Tests\Unit\Database;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;

class BlueprintTest extends TestCase
{
    public function test_raw_column_definition()
    {
        $blueprint = new Blueprint('test_table');
        $blueprint->id();
        $blueprint->raw("status ENUM('active', 'inactive') DEFAULT 'active'");
        $blueprint->raw("meta JSON");
        $blueprint->timestamps();

        $sql = $blueprint->toSql();

        $this->assertStringContainsString("status ENUM('active', 'inactive') DEFAULT 'active'", $sql);
        $this->assertStringContainsString("meta JSON", $sql);
        $this->assertStringContainsString("CREATE TABLE IF NOT EXISTS test_table", $sql);
    }
}
