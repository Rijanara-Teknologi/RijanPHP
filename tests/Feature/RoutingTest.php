<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Core\Http\Request;
use Teguh02\Rijanphp\Core\Http\Response;
use Teguh02\Rijanphp\Core\View\View;

class RoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Router::clear();
        View::clear();
        Request::$instance = null;
    }

    public function testGetRouteRegistration()
    {
        Router::get('/', function() {
            return 'Home';
        });

        $routes = Router::getRoutes();
        $this->assertArrayHasKey('GET', $routes);
        $this->assertEquals('/', $routes['GET'][0]['path']);
    }

    public function testPostRouteRegistration()
    {
        Router::post('/submit', function() {
            return 'Submitted';
        });

        $routes = Router::getRoutes();
        $this->assertArrayHasKey('POST', $routes);
    }

    public function testPutRouteRegistration()
    {
        Router::put('/update', function() {
            return 'Updated';
        });

        $routes = Router::getRoutes();
        $this->assertArrayHasKey('PUT', $routes);
    }

    public function testDeleteRouteRegistration()
    {
        Router::delete('/remove', function() {
            return 'Deleted';
        });

        $routes = Router::getRoutes();
        $this->assertArrayHasKey('DELETE', $routes);
    }

    public function testRouteGroupWithPrefix()
    {
        Router::group(['prefix' => 'api/v1'], function() {
            Router::get('users', function() {
                return 'Users';
            });
        });

        $routes = Router::getRoutes();
        $this->assertEquals('/api/v1/users', $routes['GET'][0]['path']);
    }

    public function testRouteGroupWithMiddleware()
    {
        Router::group(['middleware' => 'auth'], function() {
            Router::get('profile', function() {
                return 'Profile';
            });
        });

        $routes = Router::getRoutes();
        $this->assertContains('auth', $routes['GET'][0]['middleware']);
    }

    public function testNestedRouteGroups()
    {
        Router::group(['prefix' => 'api'], function() {
            Router::group(['prefix' => 'v1'], function() {
                Router::get('users', function() {
                    return 'Users';
                });
            });
        });

        $routes = Router::getRoutes();
        $this->assertEquals('/api/v1/users', $routes['GET'][0]['path']);
    }

    public function testNamedRoute()
    {
        Router::get('dashboard', function() {
            return 'Dashboard';
        })->name('dashboard');

        $this->assertEquals('/dashboard', Router::route('dashboard'));
    }

    public function testNamedRouteWithParameters()
    {
        Router::get('users/{id}/profile', function() {
            return 'Profile';
        })->name('user.profile');

        $this->assertEquals('/users/123/profile', Router::route('user.profile', ['id' => '123']));
    }

    public function testNamedRouteNotFound()
    {
        $this->expectException(\Exception::class);
        Router::route('nonexistent');
    }

    public function testRouteWithMiddleware()
    {
        Router::get('admin', function() {
            return 'Admin';
        })->middleware(['auth', 'admin']);

        $routes = Router::getRoutes();
        $this->assertContains('auth', $routes['GET'][0]['middleware']);
        $this->assertContains('admin', $routes['GET'][0]['middleware']);
    }

    public function testMultipleNamedRoutes()
    {
        Router::get('home', function() {
            return 'Home';
        })->name('home');

        Router::get('about', function() {
            return 'About';
        })->name('about');

        $this->assertEquals('/home', Router::route('home'));
        $this->assertEquals('/about', Router::route('about'));
    }

    public function testRouteWithMultipleParameters()
    {
        Router::get('posts/{category}/{slug}', function() {
            return 'Post';
        })->name('post.show');

        $this->assertEquals(
            '/posts/tech/laravel-tutorial',
            Router::route('post.show', ['category' => 'tech', 'slug' => 'laravel-tutorial'])
        );
    }

    public function testRootRoute()
    {
        Router::get('/', function() {
            return 'Root';
        });

        $routes = Router::getRoutes();
        $this->assertEquals('/', $routes['GET'][0]['path']);
    }

    public function testRouteWithTrailingSlash()
    {
        Router::get('test/', function() {
            return 'Test';
        });

        $routes = Router::getRoutes();
        $this->assertEquals('/test', $routes['GET'][0]['path']);
    }
}
