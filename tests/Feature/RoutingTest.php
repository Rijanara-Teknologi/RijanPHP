<?php
namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Core\Http\Request;

class RoutingTest extends TestCase
{
    public function testRouteDispatching(): void
    {
        Router::get('/hello', function () {
            return 'world';
        });

        // Simulate request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/hello';
        \Teguh02\Rijanphp\Core\Http\Request::$instance = new Request();

        $response = Router::dispatch();

        $this->assertEquals('world', $response);
    }
}
