<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Database\Migration\Migrator;
use Teguh02\Rijanphp\Master\Seeds\DatabaseSeeder;

class MigrationTest extends TestCase
{
    public function test_migrations_and_seeders()
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

        // 5. Run Seeders
        $seeder = new DatabaseSeeder();
        $seeder->run();

        // 6. Assert Data Exists
        $user = db()->table('users')->where('email', 'admin@example.com')->first();
        $this->assertNotNull($user, 'Admin user should be seeded.');
        $this->assertEquals('Admin User', $user['name']);

        $testUser = db()->table('users')->where('email', 'test@example.com')->first();
        $this->assertNotNull($testUser, 'Test user should be seeded.');
    }
}
