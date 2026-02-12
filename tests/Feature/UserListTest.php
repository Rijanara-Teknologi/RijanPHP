<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Master\Models\User;

class UserListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        setupTestDatabase();
    }

    public function test_user_list_page_loads()
    {
        // Ensure we have some users
        $userModel = new User();
        $userModel->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret'
        ]);

        $response = $this->get('/product/users');

        $response->assertStatus(200);
        $response->assertSee('User List');
        $response->assertSee('Test User');
        $response->assertSee('test@example.com');
    }
}
