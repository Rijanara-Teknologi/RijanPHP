<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Database\Migration\Migrator;
use Teguh02\Rijanphp\Master\Seeds\DatabaseSeeder;

class SeederTest extends TestCase
{
    public function test_database_seeding()
    {
        // 1. Clean up & Prepare Database
        db()->query('DROP TABLE IF EXISTS posts'); // Drop dependent table first
        db()->query('DROP TABLE IF EXISTS users');
        db()->query('DROP TABLE IF EXISTS products');
        db()->query('DROP TABLE IF EXISTS orders');
        db()->query('DROP TABLE IF EXISTS migrations');

        $migrator = new Migrator();
        $migrator->install();
        $migrationPath = dirname(__DIR__, 2) . '/Master/Migrations';
        $migrator->run([$migrationPath]);

        // 2. Run Seeders
        $seeder = new DatabaseSeeder();
        $seeder->run();

        // 3. Assert Data Exists
        $user = db()->table('users')->where('email', 'admin@example.com')->first();
        $this->assertNotNull($user, 'Admin user should be seeded.');
        $this->assertEquals('Admin User', $user['name']);

        $testUser = db()->table('users')->where('email', 'test@example.com')->first();
        $this->assertNotNull($testUser, 'Test user should be seeded.');
    }
}
