<?php

namespace Teguh02\Rijanphp\Tests\Unit\Models;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Master\Models\User;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        setupTestDatabase();
    }

    public function test_can_instantiate_user_model()
    {
        $userModel = new User();
        $this->assertInstanceOf(User::class, $userModel);
    }

    public function test_can_retrieve_users()
    {
        $userModel = new User();
        $users = $userModel->findAll();

        $this->assertIsArray($users);
        $this->assertNotEmpty($users);

        // Check if first item is object (as configured in User model)
        $this->assertIsObject($users[0]);
        $this->assertEquals('John Doe', $users[0]->name);
    }

    public function test_can_find_user_by_id()
    {
        $userModel = new User();
        $user = $userModel->find(1);

        $this->assertIsObject($user);
        $this->assertEquals('John Doe', $user->name);
    }

    public function test_can_find_users_with_where_clause()
    {
        $userModel = new User();
        // Insert a specific user to test finding
        $userModel->insert([
            'name' => 'Specific User',
            'email' => 'specific@example.com',
            'password' => 'secret'
        ]);

        $users = $userModel->where('email', 'specific@example.com')->findAll();

        $this->assertIsArray($users);
        $this->assertCount(1, $users);
        $this->assertEquals('Specific User', $users[0]->name);
        $this->assertEquals('specific@example.com', $users[0]->email);
    }

    public function test_can_find_users_with_chained_where_clauses()
    {
        $userModel = new User();

        // Insert users for chaining test
        $userModel->insert(['name' => 'User A', 'email' => 'a@example.com', 'password' => 'secret']);
        $userModel->insert(['name' => 'User B', 'email' => 'b@example.com', 'password' => 'secret']);
        $userModel->insert(['name' => 'User C', 'email' => 'c@example.com', 'password' => 'secret']);

        // Chain multiple where clauses
        // Note: The Model's `where` method forwards to QueryBuilder. 
        // We need to ensure QueryBuilder supports multiple wheres via the Model wrapper.
        // Let's assume we want to find by name AND email.

        $users = $userModel->where('name', 'User A')
            ->where('email', 'a@example.com')
            ->findAll();

        $this->assertIsArray($users);
        $this->assertCount(1, $users);
        $this->assertEquals('User A', $users[0]->name);

        // Test with no results
        $usersEmpty = $userModel->where('name', 'User A')
            ->where('email', 'b@example.com')
            ->findAll();

        $this->assertCount(0, $usersEmpty);
    }
}
