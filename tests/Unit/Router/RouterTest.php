<?php
namespace Teguh02\Rijanphp\Tests\Unit\Router;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;

class RouterTest extends TestCase
{
    public function testGetRoute(): void
    {
        Router::get('/test-route', function () {
            return 'passed';
        });

        $routes = Router::getRoutes();
        $this->assertArrayHasKey('GET', $routes);

        $found = false;
        foreach ($routes['GET'] as $route) {
            if ($route['path'] === '/test-route') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testRouteGrouping(): void
    {
        Router::group(['prefix' => 'admin'], function () {
            Router::get('/dashboard', function () {
                return 'admin dashboard';
            });
        });

        $routes = Router::getRoutes();
        $found = false;
        foreach ($routes['GET'] as $route) {
            if ($route['path'] === '/admin/dashboard') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }
}
