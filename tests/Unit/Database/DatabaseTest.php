<?php
namespace Teguh02\Rijanphp\Tests\Unit\Database;

use Teguh02\Rijanphp\Tests\TestCase;

class DatabaseTest extends TestCase
{
    public function testSelectAll(): void
    {
        $results = $this->db->table('users')->get();

        $this->assertIsArray($results);
        $this->assertCount(3, $results);
    }

    public function testSelectFirst(): void
    {
        $user = $this->db->table('users')->first();

        $this->assertIsArray($user);
        $this->assertEquals('John Doe', $user['name']);
    }

    public function testSelectWhere(): void
    {
        $user = $this->db->table('users')->where('email', 'jane@example.com')->first();

        $this->assertIsArray($user);
        $this->assertEquals('Jane Smith', $user['name']);
    }

    public function testInsert(): void
    {
        $data = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'age' => 40,
        ];

        $this->db->table('users')->insert($data);
        $user = $this->db->table('users')->where('email', 'new@example.com')->first();

        $this->assertIsArray($user);
        $this->assertEquals('New User', $user['name']);
    }

    public function testUpdate(): void
    {
        $this->db->table('users')
            ->where('email', 'john@example.com')
            ->update(['age' => 31]);

        $user = $this->db->table('users')->where('email', 'john@example.com')->first();
        $this->assertEquals(31, $user['age']);
    }

    public function testDelete(): void
    {
        $this->db->table('users')->where('email', 'john@example.com')->delete();

        $user = $this->db->table('users')->where('email', 'john@example.com')->first();
        $this->assertNull($user);
    }
}
