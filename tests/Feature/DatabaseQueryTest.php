<?php
namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;

class DatabaseQueryTest extends TestCase
{
    public function testDatabaseConnection(): void
    {
        $this->assertNotNull($this->db);
    }

    public function testBasicCRUDOperations(): void
    {
        // Create
        $this->db->table('users')->insert([
            'name' => 'Feature Test User',
            'email' => 'feature@example.com',
            'age' => 30,
        ]);

        // Read
        $user = $this->db->table('users')->where('email', 'feature@example.com')->first();
        $this->assertIsArray($user);
        $this->assertEquals('Feature Test User', $user['name']);

        // Update
        $this->db->table('users')->where('email', 'feature@example.com')->update([
            'age' => 31,
        ]);

        $updatedUser = $this->db->table('users')->where('email', 'feature@example.com')->first();
        $this->assertEquals(31, $updatedUser['age']);

        // Delete
        $this->db->table('users')->where('email', 'feature@example.com')->delete();

        $deletedUser = $this->db->table('users')->where('email', 'feature@example.com')->first();
        $this->assertNull($deletedUser);
    }
}
