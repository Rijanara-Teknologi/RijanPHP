<?php
namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Core\Http\Request;

class ProductModuleTest extends TestCase
{
    // setUp handled by parent

    public function testProductListRoute(): void
    {
        // 1. Simulate Request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/products';
        Request::$instance = new Request();

        // 2. Dispatch Router
        $response = Router::dispatch();

        // 3. Assertions
        $this->assertIsString($response);
        $this->assertStringContainsString('Our Products', $response);
        $this->assertStringContainsString('Laptop Gaming', $response);

        // Assert Layout & Partials
        $this->assertStringContainsString('RijanPHP Framework', $response); // From layout
        $this->assertStringContainsString('Home', $response); // From navigation partial
        $this->assertStringContainsString('Products', $response); // From navigation partial
    }

    public function testProductDetailByIdRoute(): void
    {
        // 1. Simulate Request for ID 1
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/product/id/1';
        Request::$instance = new Request();

        // 2. Dispatch Router
        $response = Router::dispatch();

        // 3. Assertions
        $this->assertIsString($response);
        $this->assertStringContainsString('Product Details', $response);
        $this->assertStringContainsString('Laptop Gaming', $response);

        // Assert Layout & Partials
        $this->assertStringContainsString('RijanPHP Framework', $response);
        $this->assertStringContainsString('Home', $response);
    }

    public function testProductDetailBySlugRoute(): void
    {
        // 1. Simulate Request for Slug 'mouse-wireless'
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/product/slug/mouse-wireless';
        Request::$instance = new Request();

        // 2. Dispatch Router
        $response = Router::dispatch();

        // 3. Assertions
        $this->assertIsString($response);
        $this->assertStringContainsString('Product Details', $response);
        $this->assertStringContainsString('Mouse Wireless', $response);

        // Assert Layout & Partials
        $this->assertStringContainsString('RijanPHP Framework', $response);
        $this->assertStringContainsString('Home', $response);
    }

    public function testProductCreationRoute(): void
    {
        // 1. Simulate POST Request
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/products';
        $_POST = ['name' => 'New Tablet', 'price' => 5000000];
        Request::$instance = new Request();

        // 2. Dispatch Router
        $response = Router::dispatch();

        // 3. Assertions
        $this->assertInstanceOf(\Teguh02\Rijanphp\Core\Http\Response::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('Product created successfully', $response->getContent());
        $this->assertStringContainsString('New Tablet', $response->getContent());
    }

    public function testProductUpdateRoute(): void
    {
        // 1. Simulate PUT Request
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['REQUEST_URI'] = '/product/id/1';
        Request::$instance = new Request();

        // 2. Dispatch Router
        $response = Router::dispatch();

        // 3. Assertions
        $this->assertInstanceOf(\Teguh02\Rijanphp\Core\Http\Response::class, $response);
        $this->assertStringContainsString('Product 1 updated successfully', $response->getContent());
    }

    public function testProductDeletionRoute(): void
    {
        // 1. Simulate DELETE Request
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['REQUEST_URI'] = '/product/id/1';
        Request::$instance = new Request();

        // 2. Dispatch Router
        $response = Router::dispatch();

        // 3. Assertions
        $this->assertInstanceOf(\Teguh02\Rijanphp\Core\Http\Response::class, $response);
        $this->assertStringContainsString('Product 1 deleted successfully', $response->getContent());
    }
}
