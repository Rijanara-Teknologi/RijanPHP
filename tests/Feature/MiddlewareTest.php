<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Core\Http\Request;

class MiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Router::clear();
    }

    public function test_route_middleware_can_block_access()
    {
        Router::get('/blocked', function () {
            return "Should not see this";
        })->middleware(BlockedMiddleware::class);

        // Mock request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/blocked';
        \Teguh02\Rijanphp\Core\Http\Request::$instance = new Request();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access Denied');

        Router::dispatch();
    }

    public function test_route_middleware_can_allow_access()
    {
        Router::get('/allowed', function () {
            return "Hello Allowed";
        })->middleware(AllowedMiddleware::class);

        // Mock request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/allowed';
        \Teguh02\Rijanphp\Core\Http\Request::$instance = new Request();

        ob_start();
        echo Router::dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Hello Allowed', $output);
    }

    public function test_middleware_groups_execute_all_middleware()
    {
        Router::group(['middleware' => [AllowedMiddleware::class, CounterMiddleware::class]], function () {
            Router::get('/grouped', function () {
                return "Grouped Count: " . CounterMiddleware::$count;
            });
        });

        // Mock request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/grouped';
        \Teguh02\Rijanphp\Core\Http\Request::$instance = new Request();

        CounterMiddleware::$count = 0;

        ob_start();
        echo Router::dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Grouped Count: 1', $output);
    }
}

class BlockedMiddleware
{
    public function handle()
    {
        throw new \Exception('Access Denied');
    }
}

class AllowedMiddleware
{
    public function handle()
    {
    }
}

class CounterMiddleware
{
    public static $count = 0;
    public function handle()
    {
        self::$count++;
    }
}
