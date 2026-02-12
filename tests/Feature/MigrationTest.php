<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Database\Migration\Migrator;
use Teguh02\Rijanphp\Master\Seeds\DatabaseSeeder;

class MigrationTest extends TestCase
{
    public function test_migrations_execution()
    {
        // 1. Clean up potential bootstrap tables
        db()->query('DROP TABLE IF EXISTS users');
        db()->query('DROP TABLE IF EXISTS posts');
        db()->query('DROP TABLE IF EXISTS products');
        db()->query('DROP TABLE IF EXISTS orders');
        db()->query('DROP TABLE IF EXISTS migrations');

        // 2. Initialize Migrator
        $migrator = new Migrator();

        // 2. Install Migrations Table
        $migrator->install();

        $this->assertTrue(
            \Teguh02\Rijanphp\Core\Database\Schema\Schema::hasTable('migrations'),
            'Migrations table should be created.'
        );

        // 3. Run Migrations
        // Assuming Master/Migrations path relative to base_path
        $migrationPath = dirname(__DIR__, 2) . '/Master/Migrations';
        $migrator->run([$migrationPath]);

        // 4. Assert Tables Exist
        $this->assertTrue(\Teguh02\Rijanphp\Core\Database\Schema\Schema::hasTable('users'), 'Users table should exist.');
        $this->assertTrue(\Teguh02\Rijanphp\Core\Database\Schema\Schema::hasTable('products'), 'Products table should exist.');
        $this->assertTrue(\Teguh02\Rijanphp\Core\Database\Schema\Schema::hasTable('orders'), 'Orders table should exist.');
        $this->assertTrue(\Teguh02\Rijanphp\Core\Database\Schema\Schema::hasTable('orders'), 'Orders table should exist.');
    }
}
