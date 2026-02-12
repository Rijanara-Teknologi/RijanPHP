<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;

class MasterLayoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Since routes are loaded in Register::init dynamically during app run,
        // and TestCase setup might not fully mimic modular route loading if not careful,
        // we ensure the app runs to register routes.
        // However, our TestCase simulates a request which triggers run().
    }

    public function test_master_layout_rendering()
    {
        // Define the expected output content from components
        $expectedTitle = '<title>Test Layout Page</title>';
        $expectedHeader = '<h1>Master Header Component</h1>';
        $expectedSidebar = '<h3>Sidebar Component</h3>';
        $expectedFooter = 'Master Footer Component';
        $expectedContent = '<h2>Content from Product Module</h2>';

        // Perform a GET request to the test route
        $response = $this->get('/test-master-layout');

        // Assert the response status is 200 OK
        $this->assertEquals(200, $response->getStatusCode());

        // Assert the response body contains key elements from the Master Layout components
        $this->assertStringContainsString($expectedTitle, $response->getBody());
        $this->assertStringContainsString($expectedHeader, $response->getBody());
        $this->assertStringContainsString($expectedSidebar, $response->getBody());
        $this->assertStringContainsString($expectedFooter, $response->getBody());

        // Assert the response body contains content from the Module View
        $this->assertStringContainsString($expectedContent, $response->getBody());
    }
}
