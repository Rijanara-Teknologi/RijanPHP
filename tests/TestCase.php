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

    public function call($method, $uri, $parameters = [])
    {
        // Reset state
        \Teguh02\Rijanphp\Core\Router\Router::clear();
        \Teguh02\Rijanphp\Core\View\View::clear();

        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $uri;

        if ($method === 'POST') {
            $_POST = $parameters;
        }

        // Initialize App
        $app = new \Teguh02\Rijanphp\Core\Rijan();
        $app->base_path(dirname(__DIR__));
        $app->config(dirname(__DIR__) . '/config');

        // Capture Output
        ob_start();
        $app->run();
        $content = ob_get_clean();

        $code = http_response_code();
        if ($code === false) {
            $code = 200;
        }

        return new TestResponse($content, $code);
    }

    public function get($uri)
    {
        return $this->call('GET', $uri);
    }

    public function post($uri, $data = [])
    {
        return $this->call('POST', $uri, $data);
    }
}
