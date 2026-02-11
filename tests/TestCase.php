<?php
namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Teguh02\Rijanphp\Core\Database\DatabaseManager;
use Teguh02\Rijanphp\Core\Http\Request;
use Teguh02\Rijanphp\Core\Http\Response;

abstract class TestCase extends BaseTestCase
{
    protected $db;
    protected $request;
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure APP_KEY is set for tests if not provided in env/config
        if (empty(env('APP_KEY'))) {
            putenv('APP_KEY=base64:' . base64_encode(random_bytes(32)));
        }
        $this->db = db();

        // Initialize request
        $this->request = \Teguh02\Rijanphp\Core\Http\Request::$instance ?? new \Teguh02\Rijanphp\Core\Http\Request();

        // Initialize response
        $this->response = new Response();

        // Ensure clean router state
        \Teguh02\Rijanphp\Core\Router\Router::clear();

        // Load modules to register routes
        config('modules');
    }

    protected function createTestUser(array $data = []): array
    {
        $default = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 25,
        ];

        $userData = array_merge($default, $data);
        $this->db->table('users')->insert($userData);

        return $this->db->table('users')->where('email', $userData['email'])->first();
    }
}
